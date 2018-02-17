<?php

namespace Itgro;

use Bitrix\Main\Mail\Event;
use Bitrix\Main\Mail\Mail as BitrixMail;
use CFile;

class Mail
{
    private $from;
    private $to;
    private $title;
    private $data;
    private $template;
    private $files;

    private $tmpFiles = [];

    public function from(string $from)
    {
        $this->from = $from;

        return $this;
    }

    public function to(string $to)
    {
        $this->to = $to;

        return $this;
    }

    public function withTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

    public function withData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function withTemplate(string $template, $data = null)
    {
        $this->template = $template;

        return $this->withData($data);
    }

    public function withFiles(array $files)
    {
        $this->files = $files;

        return $this;
    }

    public function send(): bool
    {
        if ($this->template) {
            return $this->sendTemplate();
        } else {
            return $this->sendRaw();
        }
    }

    private function getFiles()
    {
        if (empty($this->files)) {
            return [];
        }

        $formattedFiles = [];
        foreach ($this->files as $file) {
            $formattedFile = null;
            if (is_numeric($file)) {
                $formattedFile = CFile::GetFileArray($file);
            } elseif (array_key_exists('type', $file) && array_key_exists('name', $file)) {
                $formattedFile = CFile::GetFileArray(CFile::SaveFile($file, 'request'));

                $this->tmpFiles[] = $formattedFile['ID'];
            } elseif (array_key_exists('ID', $file) && $file['ID'] > 0) {
                $formattedFile = $file;
            }

            if (!$formattedFile) {
                continue;
            }

            $formattedFiles[] = [
                'NAME' => $formattedFile['ORIGINAL_NAME'],
                'CONTENT_TYPE' => $formattedFile['CONTENT_TYPE'],
                'ID' => $formattedFile['ID'],
                'PATH' => $_SERVER['DOCUMENT_ROOT'] . CFile::GetFileSRC($formattedFile),
            ];
        }

        return $formattedFiles;
    }

    private function removeFiles()
    {
        if (empty($this->tmpFiles)) {
            return;
        }

        // Если есть файлы, которые были добавлены в систему только ради отправки в письме - удаляем их
        foreach ($this->tmpFiles as $file) {
            if (!array_get($file, 'ID')) {
                continue;
            }

            CFile::Delete(array_get($file, 'ID'));
        }
    }

    private function sendTemplate()
    {
        $this->data = array_merge(
            [
                'FROM' => $this->from,
                'TO' => $this->to,
                'TITLE' => $this->title,
            ],
            $this->data
        );

        $result = Event::sendImmediate([
            'EVENT_NAME' => $this->template,
            'C_FIELDS' => $this->data,
            'LID' => 's1',
        ]);

        return (in_array($result, [Event::SEND_RESULT_SUCCESS, Event::SEND_RESULT_PARTLY]));
    }

    private function sendRaw()
    {
        $isSuccess = BitrixMail::send([
            'TO' => $this->to,
            'SUBJECT' => $this->title,
            'BODY' => $this->data,
            'CONTENT_TYPE' => 'html',
            'HEADER' => ['From' => $this->from],
            'CHARSET' => 'utf-8',
            'ATTACHMENT' => $this->getFiles(),
        ]);

        $this->removeFiles();

        return $isSuccess;
    }
}
