<?php

if (!function_exists('include_file')) {
    /**
     * Простое подключение файла
     *
     * @param string $path
     */
    function include_file($path)
    {
        include(sprintf('%s/%s', $_SERVER['DOCUMENT_ROOT'], $path));
    }
}

if (!function_exists('include_editable_file')) {
    /**
     * Подключение редактируемого файла
     *
     * @param string $path
     * @param string $mode
     * @param array $params
     * @return mixed
     */
    function include_editable_file($path, $mode = 'html', $params = [])
    {
        application()->IncludeFile($path, [], array_merge($params, ['MODE' => $mode]));
    }
}

if (!function_exists('include_js_body_file')) {
    /**
     * Подключает передавемые скрипты в стек, который отображается в определённом месте страницы
     * Пути можно передавать тремя способами:
     *  - строка 'path_1' - единственный путь, который будет подключен с добавлением отметки времени
     *  - массив строк ['path_1', 'path_2'] - список путей, которые будут подключены с добавлением отметки времени
     *  - массив ['path_1' => true, 'path_2' => false] - список путей, каждый из которых говорит нужно ли им добавлять отметку времени
     *
     * Возможна комбинация 2го и 3го вариантов вида ['path_1', 'path_2' => false, 'path_3']
     *
     * @param array|string $paths
     */
    function include_js_body_file($paths)
    {
        buffer_view()->start('main_script', 'body_scripts');

        foreach (array_wrap($paths) as $path => $pathOrBool) {
            if (is_bool($pathOrBool)) {
                $currentPath = ($pathOrBool) ? get_file_link_with_time($path) : $path;
            } else {
                $currentPath = get_file_link_with_time($pathOrBool);
            }

            echo sprintf('<script type="application/javascript" src="%s"></script>', $currentPath);
        }

        buffer_view()->end();
    }
}

if (!function_exists('get_files')) {
    /**
     * Файлы по их ID
     *
     * @param int|array $fileIds
     * @return array
     */
    function get_files($fileIds)
    {
        if (!$fileIds) {
            return [];
        }

        if (!is_array($fileIds) && is_numeric($fileIds)) {
            $fileIds = [$fileIds];
        }

        $rsFiles = CFile::GetList(
            [],
            [
                '@ID' => implode(',', array_filter($fileIds)),
                'ACTIVE' => 'Y',
            ]
        );

        $files = [];
        while ($file = $rsFiles->Fetch()) {
            $file['src'] = CFile::GetFileSRC($file);

            $files[$file['ID']] = $file;
        }

        return $files;
    }
}

if (!function_exists('get_file')) {
    /**
     * Массив файла по его ID
     *
     * @param int $id
     * @return mixed|null
     */
    function get_file($id)
    {
        $files = get_files([$id]);

        return (!empty($files)) ? reset($files) : null;
    }
}

if (!function_exists('get_file_link_with_time')) {
    /**
     * Добавлят к ссылке метку времени последнего изменения этого файла (чтобы корректно работло кеширование в определённых случаях)
     *
     * @param string $link
     * @return string
     */
    function get_file_link_with_time($link)
    {
        return sprintf('%s?t=%s', $link, filemtime($_SERVER['DOCUMENT_ROOT'] . $link));
    }
}
