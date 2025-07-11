<?php $this->extends('templates::examples/layout.php') ?>

<?php $this->block('content') ?>
                <div class="row">
                    <div class="col-md-12" id="div2">
                        &nbsp;
                    </div>
                    <div class="col-md-12">
                        <select class="form-select" id="colorselect" name="colorselect"
                                <?= attr()->on('change', rq()->setColor(je('colorselect')->rd()->select())) ?>>
                            <option value="black" selected="selected">Black</option>
                            <option value="red">Red</option>
                            <option value="green">Green</option>
                            <option value="blue">Blue</option>
                        </select>
                    </div>
                    <div class="col-md-12 buttons">
                        <button type="button" class="btn btn-primary" <?= attr()->click(rq()->helloWorld(1)) ?> >CLICK ME</button>
                        <button type="button" class="btn btn-primary" <?= attr()->click(rq()->helloWorld(0)) ?> >Click Me</button>
                    </div>
                </div>
<?php $this->endblock() ?>

<?php $this->block('code') ?>
                <div class="card code">
                    <div class="card-body">
                        <?= highlight_file(__DIR__ . '/code.php', true) ?>
                    </div>
                </div>
<?php $this->endblock() ?>

<?php $this->block('javascript') ?>
<script type='text/javascript'>
    /* <![CDATA[ */
    window.onload = function() {
        // Call the HelloWorld class to populate the 2nd div
        <?= rq()->helloWorld(0) ?>;
        // call the HelloWorld->setColor() method on load
        <?= rq()->setColor(je('colorselect')->rd()->select()) ?>;
    }
    /* ]]> */
</script>
<?php $this->endblock() ?>
