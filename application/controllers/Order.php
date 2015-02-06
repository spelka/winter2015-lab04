<?php

/**
 * Order handler
 * 
 * Implement the different order handling usecases.
 * 
 * controllers/welcome.php
 *
 * ------------------------------------------------------------------------
 */
class Order extends Application {

    function __construct() {
        parent::__construct();
    }

    // start a new order
    function neworder() {
        //get the new order number
		$order_num = $this->orders->highest() + 1;

		//create a new order object
		$record = $this->orders->create();
		
		//set the properties of the order
		$record->num = $order_num;
		$record->date = date("l jS \of F Y h:i:s A");
		$record->status = 'Y';
		
		$this->orders->add($record);
			
        redirect('/order/display_menu/' . $order_num);
    }

    // add to an order
    function display_menu($order_num = null) {
        if ($order_num == null)
            redirect('/order/neworder');

        $this->data['pagebody'] = 'show_menu';
        $this->data['order_num'] = $order_num;
        //FIXME
		//get the order from the database
		$order = $this->orders->get($order_num);

        // Make the columns
        $this->data['meals'] = $this->make_column('m');
        $this->data['drinks'] = $this->make_column('d');
        $this->data['sweets'] = $this->make_column('s');

        $this->render();
    }

    // make a menu ordering column
    function make_column($category) {
        //FIXME
		$items = $this->menu->some('category' , $category);
        return $items;
    }

    // add an item to an order
    function add($order_num, $item) {
        //FIXME
        redirect('/order/display_menu/' . $order_num);
    }

    // checkout
    function checkout($order_num) {
        $this->data['title'] = 'Checking Out';
        $this->data['pagebody'] = 'show_order';
        $this->data['order_num'] = $order_num;
        //FIXME

        $this->render();
    }

    // proceed with checkout
    function proceed($order_num) {
        //FIXME
        redirect('/');
    }

    // cancel the order
    function cancel($order_num) {
        //FIXME
        redirect('/');
    }

}
