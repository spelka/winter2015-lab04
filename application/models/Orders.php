<?php

/**
 * Data access wrapper for "orders" table.
 *
 * @author jim
 */
class Orders extends MY_Model {

    // constructor
    function __construct() {
        parent::__construct('orders', 'num');
    }

    // add an item to an order
    function add_item($num, $code) {
        
    }

    // calculate the total for an order
    function total($num) {
		$order_total = 0.0;
		//get the list of items from the orderitems database table
		$orderItemArray = $this->orderitems->some( 'order' , $num );
		
		//iterate through the items and find the associated menu price
		foreach($orderItemArray as $order)
		{
			$item = $this->menu->get( 'code' , $order->item);
			$order_total += ($item->price * $order->quantity);
		}
		
		//return the total
        return money_format($order_total);
    }

    // retrieve the details for an order
    function details($num) {
        
    }

    // cancel an order
    function flush($num) {
        
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num) {
        return false;
    }

}
