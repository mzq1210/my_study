#### YII2 JS BLOCK

```PHP
<?php

namespace h5\widgets;

use yii\web\View;

/**
 * JsBlock
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class JsBlock extends \yii\base\Widget
{
    public $key;

    /**
     * POS_HEAD（head结束前）;
     * POS_BIGIN（body开始处）;
     * POS_END（body结束前）;
     * POS_READY(包括到jquery中，表示文档加载后);
     * POS_LOAD（包括到jquery中，文档加载时）；
     */
    public $pos = View::POS_READY;
    public $viewFile;
    public $viewParams = [];

    /**
     * Starts recording a block.
     */
    public function init()
    {
        if ($this->viewFile === null) {
            ob_start();
            ob_implicit_flush(false);
        }
    }

    /**
     * Ends recording a block.
     * This method stops output buffering and saves the rendering result as a named block in the view.
     */
    public function run()
    {
        if ($this->viewFile === null) {
            $block = ob_get_clean();
        } else {
            $block = $this->view->render($this->viewFile, $this->viewParams);
        }
        $block = trim($block);

        /**
         * Thanks to yiqing
         * http://www.yiiframework.com/wiki/752/embedded-javascript-block-in-your-view-with-ide-checking-or-intellisense/
         */
        $jsBlockPattern = '|^<script[^>]*>(?P<block_content>.+?)</script>$|is';
        if (preg_match($jsBlockPattern, $block, $matches)) {
            $block = $matches['block_content'];
        }

        $this->view->registerJs($block, $this->pos, $this->key);
    }
}
```

使用：

```PHP
<?php JsBlock::begin() ?>
    <script>
        $(function () {
            jQuery('form#apitool').on('beforeSubmit', function (e) {
                var $form = $(this);
                $.ajax({
                    url: $form.attr('action'),
                    type: 'post',
                    data: $form.serialize(),
                    success: function (data) {
                        // do something
                    }
                });
            }).on('submit', function (e) {
                e.preventDefault();
            });
         })
    </script>
<?php JsBlock::end() ?>
```

