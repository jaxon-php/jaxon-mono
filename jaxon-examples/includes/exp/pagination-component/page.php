<?php $this->extends('examples::layout.php') ?>

<?php
use function Jaxon\attr;
use function Jaxon\rq;

$rqPageContent = rq(PageContent::class);
?>

<?php $this->block('content') ?>
                <div class="row">
                    <!-- Custom attribute: Component for paginated content. -->
                    <div class="col-md-12" <?php echo attr()->bind($rqPageContent) ?>>
                    </div>
                    <!-- Custom attribute: Component for pagination links. -->
                    <div class="col-md-12" <?php echo attr()->pagination($rqPageContent) ?>>
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
        <?php echo $rqPageContent->showPage(1, 'This is the page title') ?>;
    }
    /* ]]> */
</script>
<?php $this->endblock() ?>
