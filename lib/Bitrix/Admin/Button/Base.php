<?php

namespace Itgro\Bitrix\Admin\Button;

abstract class Base
{
    protected $properties;
    protected $view;

    public function withProperties($properties)
    {
        $this->properties = $properties;

        return $this;
    }

    public function withView($view)
    {
        $this->view = $view;

        return $this;
    }

    public function render()
    {
        $link = $this->createRelatedLink();

        $this->addButton($link);
    }

    abstract protected function getLinkReplaces();

    private function createRelatedLink()
    {
        $replaces = $this->getLinkReplaces();

        if (empty($replaces)) {
            return null;
        }

        return call_user_func_array('sprintf', $replaces);
    }

    private function addButton($link)
    {
        ?>

        <script type="application/javascript">
            $(document).ready(function () {
                var copyButton = $('.adm-btn-copy'),
                    newButton = '<a ' +
                        'class="adm-btn <?= (array_get($this->view, 'style')) ? array_get($this->view, 'style') : ''; ?>" ' +
                        'target="_blank" ' +
                        'href="<?= $link; ?>"><?= array_get($this->view, 'name'); ?></a>';

                if (!copyButton || copyButton.length <= 0) {
                    return;
                }

                <?php if (array_get($this->view, 'place') === 'after'): ?>
                copyButton.after(newButton);
                <?php else: ?>
                copyButton.before(newButton);
                <?php endif; ?>
            });
        </script>

        <?php
    }
}
