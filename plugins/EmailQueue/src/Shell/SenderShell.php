<?php
namespace EmailQueue\Shell;

use Cake\Console\Shell;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Mailer\Email;
use Cake\Core\Configure;

class SenderShell extends Shell {

    public function getOptionParser() {
        $parser = parent::getOptionParser();
        $parser
                ->description('Sends queued emails in a batch')
                ->addOption('limit', array(
                    'short' => 'l',
                    'help' => 'How many emails should be sent in this batch?',
                    'default' => 50
                ))
                ->addOption('template', array(
                    'short' => 't',
                    'help' => 'Name of the template to be used to render email',
                    'default' => 'default'
                ))
                ->addOption('layout', array(
                    'short' => 'w',
                    'help' => 'Name of the layout to be used to wrap template',
                    'default' => 'default'
                ))
                ->addOption('config', array(
                    'short' => 'c',
                    'help' => 'Name of email settings to use as defined in email.php',
                    'default' => 'default'
                ))
                ->addSubCommand('clearLocks', array(
                    'help' => 'Clears all locked emails in the queue, useful for recovering from crashes'
                ));
        return $parser;
    }

    /**
     * Sends queued emails
     *
     * @access public
     */
    public function main() {
        //Configure::write('App.baseUrl', '/');
        $emailQueue = TableRegistry::get('EmailQueue.EmailQueues');

        $emails = $emailQueue->getBatch($this->params['limit']);
        
        //print_r($emails);
        foreach ($emails as $e) {
            $configName = $e['EmailQueue']['config'] === 'default' ? $this->params['config'] : $e['EmailQueue']['config'];
            $template = $e['EmailQueue']['template'] === 'default' ? $this->params['template'] : $e['EmailQueue']['template'];
            $layout = $e['EmailQueue']['layout'] === 'default' ? $this->params['layout'] : $e['EmailQueue']['layout'];

            try {
                $email = $this->_newEmail($configName);

                if (!empty($e['EmailQueue']['from_email']) && !empty($e['EmailQueue']['from_name'])) {
                    $email->from($e['EmailQueue']['from_email'], $e['EmailQueue']['from_name']);
                }
                
                if(isset($e['EmailAttachment']) && is_array($e['EmailAttachment']) && !empty($e['EmailAttachment'])) {
                    $attachments = array();
                    foreach ($e['EmailAttachment'] as $a) {
                        $attachments[$a['filename']] = array('file'=>$a['filepath'], 'mimetype'=>$a['mimetype']);
                    }
                    
                    $email->attachments($attachments);
                }
                
                $lang = $e['EmailQueue']['lang'];
                if($lang != null) {
                    Configure::write ('Config.language', $lang);
                    ini_set('intl.default_locale', $lang);
                }
                
                $this->out('Email language is '.$lang.', language '. Configure::read('Config.language').' was set');

                $sent = $email
                        ->to($e['EmailQueue']['to_inbox'])
                        ->subject($e['EmailQueue']['subject'])
                        ->template($template, $layout)
                        ->emailFormat($e['EmailQueue']['format'])
                        ->viewVars($e['EmailQueue']['template_vars'])
                        ->send();
                
            } catch (SocketException $exception) {
                $this->err($exception->getMessage());
                $sent = false;
            }

            if ($sent) {
                $emailQueue->success($e['EmailQueue']['id']);
                $this->out('<success>Email ' . $e['EmailQueue']['id'] . ' was sent</success>');
            } else {
                $emailQueue->fail($e['EmailQueue']['id']);
                $this->out('<error>Email ' . $e['EmailQueue']['id'] . ' was not sent</error>');
            }
            
            Configure::write('Config.language', Configure::read('default_language'));
        }
        if(!empty($emails)) $emailQueue->releaseLocks(Hash::extract($emails, '{n}.EmailQueue.id'));
    }

    /**
     * Clears all locked emails in the queue, useful for recovering from crashes
     *
     * @return void
     * */
    public function clearLocks() {
        ClassRegistry::init('EmailQueue.EmailQueue')->clearLocks();
    }

    /**
     * Returns a new instance of CakeEmail
     *
     * @return CakeEmail
     * */
    protected function _newEmail($config) {
        return new Email($config);
    }

}