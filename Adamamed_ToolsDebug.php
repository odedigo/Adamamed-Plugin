<?php

class Adamamed_ToolsDebugPage {

    public function doPage() {
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['todo']) ) {
            $action = $_POST['todo'];
            if ($action == 'turnoff') {
                $this->debugModeChange(false);
            }
            else if ($action == 'turnon') {
                $this->debugModeChange(true);
            }
        }

        ?>
        <div class='wrap'>
            <p class='ad-notice'>
            <?php 
            $action = 'turnon';
            $label = 'Turn Debug On';
            if( WP_DEBUG === true ) : 
                $action = 'turnoff';
                $label = 'Turn Debug Off';
                ?>
                Debug mode is ON. Click on the button to turn it off.</p>
            <?php else : ?>
                Debug mode is OFF. Click on the button to turn it on.</p>
            <?php endif; ?>
            <form method='post'>
                <input type="hidden" name='todo' value='<?php echo $action;?>'/>
                <p><button type='submit' value="submit"><?php echo $label; ?></button></p>
            </form>
        </div>  
        <?php        
    }

    protected function debugModeChange($turnon) {
        if ($turnon == false) {           
            $lookfor = "define('WP_DEBUG', true);";
            $replaceWith = "define('WP_DEBUG', false);";
            $msg = "OFF";
        }
        else {
            $lookfor = "define('WP_DEBUG', false);";
            $replaceWith = "define('WP_DEBUG', true);";
            $msg = "ON";
        }

        $path = get_home_path() . "wp-config.php";
        $backup = get_home_path() . "wp-config.backup.php";
        if (!file_exists($backup)) {
            copy($path, $backup);
        }
        $data = file_get_contents($path);
        $pos = strpos($data, $lookfor);
        
        if ($pos > 0) {
            $newConfig = str_replace($lookfor,$replaceWith,$data);
            file_put_contents($path,$newConfig);
            echo "<p class='ad-notice'>Debug mode changed to $msg</p>";
        }
        else {
            echo "<p class='ad-error'>Could not modify settings</p>";
        }
        ?>
        <script type="text/javascript">
            document.location.reload(true);
        </script>
        <?php
    }
}
?>