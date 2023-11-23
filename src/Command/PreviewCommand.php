<?php

declare(strict_types=1);

namespace EmailQueue\Command;

use Cake\Command\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\Mailer\Mailer;
use Cake\ORM\TableRegistry;

use Cake\Mailer\TransportFactory;



class PreviewCommand extends Command
{

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->addArgument('uid', [
                'help' => 'What is the uid of the email?',
                'required' => true
            ]);
        return $parser;
    }


    public function execute(Arguments $args, ConsoleIo $io): int
    {

        $uid = $args->getArgument('uid');
        $io->out('Preview ' . $uid);

        $conditions['id IN'] = $uid;

        $emailQueue = TableRegistry::getTableLocator()->get('EmailQueue.EmailQueue');
        $email = $emailQueue->get($uid);

        if (!$email) {
            $io->out('Email not found');
            return static::CODE_ERROR;
        }
        $configName = $email->config;
        $template = $email->template;
        $layout = $email->layout;
        $headers = empty($email->headers) ? [] : (array)$email->headers;
        $theme = empty($email->theme) ? '' : (string)$email->theme;
    

        $mailer = new Mailer($configName);

        if (!empty($email->attachments)) {
            $mailer->setAttachments($email->attachments);
        }

        TransportFactory::setConfig('Debug', [
            'className' => 'Debug'
        ]);

        $mailer->setTransport('Debug')
            ->setTo($email->email)
            ->setSubject($email->subject)
            ->setEmailFormat($email->format)
            ->addHeaders($headers)
            ->setMessageId(false)
            ->setReturnPath($mailer->getFrom())
            ->setViewVars($email->template_vars)
            ->viewBuilder()
            ->setTemplate("welcome")
        ->setLayout($layout);

        $return = $mailer->deliver();

        // $return = $mailer->setFrom(['me@example.com' => 'My Site'])
        // ->setTo('you@example.com')
        // ->setSubject('About')
        // ->deliver('My message');

        $io->out('Content:');
        $io->hr();
        $io->out($return['message']);
        $io->hr();
        $io->out('Headers:');
        $io->hr();
        $io->out($return['headers']);
        $io->hr();
        $io->out('Data:');
        $io->hr();
        debug($email->template_vars);
        $io->hr();
        $io->out('');
        return static::CODE_SUCCESS;
    }
}
