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
		$CI = &get_instance();
		$CI->load->model('orderitems');
		$CI->load->model('menu');
    }

    // add an item to an order
    function add_item($num, $code)
	{
        $CI = get_instance();
		//if the item is already part of the order
		if ( $CI -> orderitems -> exists ( $num, $code ))
		{
			//retrieve the record, increment quantity, update table
			$record = $CI -> orderitems -> get ( $num, $code);
			$record -> quantity ++;
			$CI -> orderitems -> update	( $record );
		}
		else
		{
			//else make an empty orderitem record, and populate its fields, qty = 1
			$record = $CI -> orderitems -> create();
			$record -> order = $num;
			$record -> item = $code;
			$record -> quantity = 1;
			$CI -> orderitems -> add ( $record );
		}
    }

    // calculate the total for an order
    function total($num) {
		
		//get all the items in order
		$items = $this->orderitems->some('order', $num);
		
		//add them up
		$result = 0.0;
		foreach ($items as $item)
		{
			$menuitem = $this->menu->get($item->item);
			$result += $item->quantity * $menuitem->price;
		}

		//$order is a record from the Orders table given by key $num
		$order = $this->orders->get($num);
		//set the total in the record to the price calculated above
		$order->total = $result;
		//update the RDB record with the new information
		$this->orders->update($order);
		
		return $result;
    }

    // retrieve the details for an order
    function details($num)
	{
        $details = $this -> orderitems -> some ( 'order' , $num );
		
		foreach ( $details as $entry )
		{
			$item = $this -> menu -> get ( $entry -> item);
			$entry -> picture = $item -> picture;
			$entry -> description = $item-> description;
		}
		
		return $details;
    }

    // cancel an order
    function flush($num) {
        
    }

    // validate an order
    // it must have at least one item from each category
    function validate($num)
	{
		$CI = & get_instance();
		$items = $CI ->orderitems -> group ( $num );
		$gotem = array();
		if ( count ( $items ) > 0 )
		{
			foreach ( $items as $item )
			{
				$menu = $CI -> menu -> get ( $item -> item );
				$gotem [ $menu -> category ] = 1;
			}
		}
		if (($gotem['m'] == 1) && ($gotem['d'] == 1) && ($gotem['s'] == 1))
		{
			return true;
		}
		else
			return false;
		
    }

}
