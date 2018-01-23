<?php


class Adamamed_Manual_Order {

    public function drawPage($plugin) {

        $this->handleFormSumbission();

        echo "<div class='wrap'><h2>הזמנות ידניות</h2>"; 
        echo "<h4>רשימת הזמנות ידניות במערכת</h4>";

        $manualOrders = $this->getManualOrders();

        //print_r($manualOrders);
        $form_nonce = wp_create_nonce( 'nds_add_user_meta_form_nonce' ); 
        ?>
        <!-- <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" id="manual_orders_form" >			-->
        
        <!-- <input type="hidden" name="action" value="manual_orders_action">
		<input type="hidden" name="manual_orders_form_nonce" value="<?php //echo $form_nonce ?>" />			-->
			

        <table cellspacing='1' cellpadding='2' border='1'>
            <tr>
                <th>ID</th>
                <th>אימייל</th>
                <th>כמות</th>
                <th>מוצר</th>
                <th>אסמכתא</th>
                <th>תאריך בפורמט 23-01-2018</th>
                <th>פעולות</th>
            </tr>
        
        <?php foreach ($manualOrders as $order) : ?>
        <form method="POST">
        <input type="hidden" name="manual_orders" value="manual_orders">
        <input type="hidden" name="oid" value="<?php echo $order->order_id;?>">
        <tr>
            <!-- ID -->
            <td><?php echo $order->order_id;?></td>
            <!-- email -->
            <td><input type="text" size="20" name='email' value="<?php echo $order->email;?>"></td>
            <!-- quantity -->
            <td><input type="text" size="4" name='quantity' value="<?php echo $order->quantity;?>"></td>
            <!-- product -->
            <td><input type="text" name='product' value="<?php echo $order->product;?>"></td>
            <!-- ref -->
            <td><input type="text" size="20" name='reference' value="<?php echo $order->reference;?>"></td>
            <!-- date -->
            <td><input type="text" size="20" name='date' value="<?php echo $this->reverseDate($order->date,true);?>"></td>
            <!-- comment -->
            <td><input type="text" size="40" name='comment' value="<?php echo $order->comment;?>"></td>
            <!-- Action -->
            <td>
            <button name='action_delete' onclick='return confirmDeleteSubmit("<?php echo $order->email;?>");'>מחיקה</button>&nbsp;&nbsp;
            <button name='action_update' onclick='return confirmUpdateSubmit("<?php echo $order->email;?>");'>עדכון</button>
            </td>
        </tr>
        </form>
        <?php endforeach; ?>

        <tr>
            <td colspan="7" style='text-align:center;font-weight:bold'>הוספת הזמנה ידנית חדשה</td>
        </tr>
        <form method="POST">
        <input type="hidden" name="manual_orders" value="manual_orders">
        <tr>
            <!-- ID -->
            <td></td>
            <!-- email -->
            <td><input type="text" size="20" name='email' ></td>
            <!-- quantity -->
            <td><input type="text" size="4" name='quantity' ></td>
            <!-- product -->
            <td><input type="text" name='product' ></td>
            <!-- ref -->
            <td><input type="text" size="20" name='reference' ></td>
            <!-- date -->
            <td><input type="text" size="20" name='date' ></td>
            <!-- date -->
            <td><input type="text" size="40" name='comment' ></td>
            <!-- Action -->
            <td>
            <button name='action_new'>הוספה</button>&nbsp;&nbsp;
            </td>
        </tr>
        </form>
        </table>
        </form>
        <?php

        echo "</div>";
    }

    protected function getManualOrders() {
        $db = new Adamamed_DB();
        $orders = $db->getManualOrders();
        return $orders;
    }

    protected function handleFormSumbission() {
        if (!isset($_POST['manual_orders'])) {
           return;
        }
        $data = array();
        $id = -1;
        if (isset($_POST['oid']))
            $id = $_POST['oid'];
        $action = '';
        foreach ($_POST as $key => $value) {
            //echo '<p>'.$key.' - '.$value.'</p>';
            if ($key == 'action_delete')
                $action = 'delete';
            else if ($key == 'action_update')
                $action = 'update';
            else if ($key == 'action_new')
                $action = 'new';      
               
            if ($key == 'date')    
                $value = $this->reverseDate($value,false);
            $data[$key] = $value;
        }

        if ($action == '') {
            $this->msg("Error parsing form");
            return;
        }

        $db = new Adamamed_DB();
        if ($action == 'delete' && $id >= 0) {
            $db->deleteManualOrder($id);
        }
        else if ($action == 'update' && $id >= 0) {
            if ($this->validateData($data))
                $db->updateManualOrder($id, $data);
        }
        else if ($action == 'new') {
            if ($this->validateData($data))
                $db->insertManualOrder($data);
        }

    }

    protected function validateData($data) {
        $ok = true;
        if (!isset($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $ok = false;
            $this->msg("כתובת אימייל לא חוקית");
        }
        if (!isset($data['quantity']) || !is_numeric($data['quantity']) || $data['quantity']<1) {
            $ok = false;
            $this->msg("כמות לא חוקית");
        }
        if (!isset($data['product']) || $data['product'] == '') {
            $ok = false;
            $this->msg("מוצר לא חוקי");
        }
        if (!isset($data['reference']) || $data['reference'] == '') {
            $data['reference'] = '0';
        }
        if (!isset($data['date']) || $data['date'] == '' || !$this->isDate($data['date'])) {
            $ok = false;
            $this->msg("תאריך לא חוקי".$data['date']);
        }
        return $ok;
    }

    protected function reverseDate($date, $fromDB) {
        $parts = explode('-',$date);
        if ($fromDB)
            $d = $parts[2].'-'.$parts[1].'-'.$parts[0];
        else
            $d = $parts[2].'-'.$parts[1].'-'.$parts[0];
        return $d;
    }

    protected function isDate($date) {
        $parts = explode('-',$date);
        if (sizeof($parts) != 3)
            return false;
        if (!is_numeric($parts[0]) || strlen($parts[0]) != 4)
            return false;
        if (!is_numeric($parts[1]) || strlen($parts[1]) != 2 || $parts[1] < 0 || $parts[1] > 12)
            return false;
        if (!is_numeric($parts[2]) || strlen($parts[2]) != 2 || $parts[2] < 0 || $parts[2] > 31)
            return false;
        return true;
    }

    protected function msg($str, $isError = true) {
        echo "<p style='font-weight:bold;";
        if ($isError)
            echo "color:red;";
        echo "'>$str</p>";
    }
}
?>