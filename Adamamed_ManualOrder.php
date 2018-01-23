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
                <th>תאריך</th>
                <th>פעולות</th>
            </tr>
        
        <?php foreach ($manualOrders as $order) : ?>
        <form method="POST">
        <input type="hidden" name="manual_orders" value="manual_orders">
        <tr>
            <!-- ID -->
            <td><?php echo $order->order_id;?></td>
            <!-- email -->
            <td><input type="text" size="20" name='email_<?php echo $order->order_id;?>' value="<?php echo $order->email;?>"></td>
            <!-- quantity -->
            <td><input type="text" size="4" name='quantity_<?php echo $order->order_id;?>' value="<?php echo $order->quantity;?>"></td>
            <!-- product -->
            <td><input type="text" name='product_<?php echo $order->order_id;?>' value="<?php echo $order->product;?>"></td>
            <!-- ref -->
            <td><input type="text" size="20" name='reference_<?php echo $order->order_id;?>' value="<?php echo $order->reference;?>"></td>
            <!-- date -->
            <td><input type="text" size="20" name='date_<?php echo $order->order_id;?>' value="<?php echo $order->date;?>"></td>
            <!-- Action -->
            <td>
            <button name='del_<?php echo $order->order_id;?>' id='delete'>מחיקה</button>&nbsp;&nbsp;
            <button name='upd_<?php echo $order->order_id;?>'>עדכון</button>
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
            <td><input type="text" size="20" name='email_new' ></td>
            <!-- quantity -->
            <td><input type="text" size="4" name='quantity_new' ></td>
            <!-- product -->
            <td><input type="text" name='product_new' ></td>
            <!-- ref -->
            <td><input type="text" size="20" name='reference_new' ></td>
            <!-- date -->
            <td><input type="text" size="20" name='date_new' ></td>
            <!-- Action -->
            <td>
            <button name='new' id='new'>הוספה</button>&nbsp;&nbsp;
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
        $action = '';
        foreach ($_POST as $key => $value) {
            //echo '<p>'.$key.' - '.$value.'</p>';
            $items = explode('_', $key);
            if (sizeof($items) == 2 && $items[0] == 'quantity') {
                $id = $items[1];
            }
            if ($items[0] == 'del')
                $action = 'delete';
            else if ($items[0] == 'upd')
                $action = 'update';
            else if ($items[0] == 'new')
                $action = 'new';      
               
            if (sizeof($items) == 2)
                $data[$items[0]] = $value;
        }

        echo "<p style='text-align:right;direction:ltr'>";
        echo "Action: $action       id: $id<br>";
        print_r($data);
        echo "</p>";
        if ($action == 'delete') {
            if ($this->validateData($data))
                $db->deleteManualOrder($id);
        }
        else if ($action == 'update') {
            if ($this->validateData($data))
                db->updateManualOrder($id, $data);
        }
        else if ($action == 'new') {
            if ($this->validateData($data))
                $db->insertManualOrder($data);
        }

    }

    protected function validateData($data) {
        $email = $data['email'];
        return false;
    }

}
?>