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
		$order = $this->orders->get($order_num);		//order is the order object here
		$this->data['title'] = "Order # " . $order->num . ": Total ($" . sprintf('%0.2f', $order->total) . ")";

        // Make the columns
        $this->data['meals'] = $this->make_column('m');
        $this->data['drinks'] = $this->make_column('d');
        $this->data['sweets'] = $this->make_column('s');

	// Bit of a hokey patch here, to work around the problem of the template
	// parser no longer allowing access to a parent variable inside a
	// child loop - used for the columns in the menu display.
	// this feature, formerly in CI2.2, was removed in CI3 because
	// it presented a security vulnerability.
	// 
	// This means that we cannot reference order_num inside of any of the
	// variable pair loops in our view, but must instead make sure
	// that any such substitutions we wish make are injected into the 
	// variable parameters
	// Merge this fix into your origin/master for the lab!
	$this->hokeyfix($this->data['meals'],$order_num);
	$this->hokeyfix($this->data['drinks'],$order_num);
	$this->hokeyfix($this->data['sweets'],$order_num);
	// end of hokey patch
	
        $this->render();
    }

    // inject order # into nested variable pair parameters
    function hokeyfix($varpair,$order) {
	foreach($varpair as &$record)
	    $record->order_num = $order;
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
		//add an item
		$this->orders->add_item($order_num, $item);
		
		//update the price
		$this->orders->total($order_num);
		
        redirect('/order/display_menu/' . $order_num);
    }

    // checkout
    function checkout($order_num) {
        $this->data['title'] = 'Checking Out';
        $this->data['pagebody'] = 'show_order';
        $this->data['order_num'] = $order_num;
        //FIXME
		
		$this -> data['total'] = number_format ($this -> orders -> total( $order_num ), 2);
		
		$items = $this -> orderitems -> group ( $order_num );
		
		foreach ( $items as $item )
		{
			$menuitem = $this -> menu -> get ( $item -> item );
			$item -> code = $menuitem -> name;
		}
		
		$this -> data['items'] = $items;
		
		//ensure one thing from each type is in the order
		$this -> data['okornot'] = $this -> orders -> validate ( $order_num );

        $this->render();
    }

    // proceed with checkout
    function commit($order_num)
	{
        //FIXME
		if ( $this -> orders -> validate ( $order_num ) );
		{
			redirect ('/order/display_menu/' . $order_num);
		}
		//get the order entry in the RDB
		$record  = $this -> orders -> get ( $order_num );
		//set its date
		$record -> date = date(DATE_ATOM);
		//set its status to complete
		$record -> status = 'c';
		//set the order total for the record in the RDB
		$record -> total = $this  -> orders -> total ( $order_num );
		//update the RDB entry for this order
		$this -> orders -> update ( $record );
        redirect('/');
    }

    // cancel the order
    function cancel($order_num)
	{
        //FIXME
		//delete items in our order
		$this -> orderitems -> delete_some ( $order_num );
		//get the order entry itself
		$record = $this -> orders -> get ( $order_num );
		//set status to cancelled
		$record -> status = 'x';
		//update the RDB
		$this -> orders -> update ( $record );
        redirect('/');
    }

}
