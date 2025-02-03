<?php

require __DIR__ . '/defs.php';
require dirname(__DIR__, 3) . '/includes/header.php';

?>

    <div class="container-fluid">
<?php require dirname(__DIR__, 3) . '/includes/nav.php' ?>

        <div class="row">
            <div class="col-md-4 exp-form">
<?php require dirname(__DIR__, 3) . '/includes/title.php' ?>

                <div class="row">
                    <div class="col-md-12" id="div1">
                        &nbsp;
                    </div>
                    <div class="col-md-12">
                        <select class="form-control" id="colorselect1" name="colorselect1"
                                onchange="App.Test.Test.setColor(jaxon.$('colorselect1').value); return false;">
                            <option value="black" selected="selected">Black</option>
                            <option value="red">Red</option>
                            <option value="green">Green</option>
                            <option value="blue">Blue</option>
                        </select>
                    </div>
                    <div class="col-md-12 buttons">
                        <button type="button" class="btn btn-primary" onclick='App.Test.Test.sayHello(1); return false;' >CLICK ME</button>
                        <button type="button" class="btn btn-primary" onclick='App.Test.Test.sayHello(0); return false;' >Click Me</button>
                        <button type="button" class="btn btn-primary" onclick="App.Test.Test.showDialog(); return false;" >Tingle Dialog</button>
                    </div>

                    <div class="col-md-12" id="div2">
                        &nbsp;
                    </div>
                    <div class="col-md-12">
                        <select class="form-control" id="colorselect2" name="colorselect2"
                                onchange="Ext.Test.Test.setColor(jaxon.$('colorselect2').value); return false;">
                            <option value="black" selected="selected">Black</option>
                            <option value="red">Red</option>
                            <option value="green">Green</option>
                            <option value="blue">Blue</option>
                        </select>
                    </div>
                    <div class="col-md-12 buttons">
                        <button type="button" class="btn btn-primary" onclick="Ext.Test.Test.sayHello(1); return false;" >CLICK ME</button>
                        <button type="button" class="btn btn-primary" onclick="Ext.Test.Test.sayHello(0); return false;" >Click Me</button>
                        <button type="button" class="btn btn-primary" onclick="Ext.Test.Test.showDialog(); return false;" >Bootstrap Dialog</button>
                    </div>
                </div>
            </div> <!-- class="exp-form" -->

            <div class="col-md-8 exp-code">
<?php require dirname(__DIR__, 3) . '/includes/code.php' ?>
            </div>
       </div> <!-- class="row" -->
    </div>

<script type='text/javascript'>
    /* <![CDATA[ */
    window.onload = function() {
        // call the helloWorld function to populate the div on load
        App.Test.Test.sayHello(0, false);
        // call the setColor function on load
        App.Test.Test.setColor(jaxon.$('colorselect1').value, false);
        // Call the HelloWorld class to populate the 2nd div
        Ext.Test.Test.sayHello(0, false);
        // call the HelloWorld->setColor() method on load
        Ext.Test.Test.setColor(jaxon.$('colorselect2').value, false);
    }
    /* ]]> */
</script>

<?php require dirname(__DIR__, 3) . '/includes/footer.php' ?>
