<?php
/*
 * @see https://wordpress.org/plugins/custom-list-table-example/
 * @see http://www.smashingmagazine.com/2011/11/native-admin-tables-wordpress/
 */

/*
 * Load the base class
 */
if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

abstract class WPLDK_Table_List extends WP_List_Table {
	
	private $per_page = 5;
	
	function __construct($options, $per_page = 10) {
		global $status, $page;
		
		//Set parent defaults
		parent::__construct($options);
		
		if ($per_page) {
			$this->per_page = $per_page;
		}
	}

	function column_default($item, $column_name){
		return $item[$column_name];
	}
	
	function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
	}
/*
function display_tablenav( $which ) 
{
    ?>
    <div class="tablenav <?php echo esc_attr( $which ); ?>">

        <div class="alignleft actions">
            <?php $this->bulk_actions(); ?>
        </div>
        <?php
        $this->extra_tablenav( $which );
        $this->pagination( $which );
        ?>
        <br class="clear" />
    </div>
    <?php
}	
*/	
	function prepare_items($whereCondition = NULL) {
		global $wpdb;

		/* -- Preparing your query -- */
		$query = "SELECT * FROM xb_spg_".$this->_args['plural'];
		if ($whereCondition) {
			$query .= ' WHERE '.$whereCondition;
		}
		
		/* -- Ordering parameters -- */
		$orderby = !empty($_GET["orderby"]) ? addslashes($_GET["orderby"]) : 'ASC';
		$order = !empty($_GET["order"]) ? addslashes($_GET["order"]) : '';
		if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }

		/* -- Pagination parameters -- */
		//Number of elements in your table?
		$total_items = $wpdb->query($query); //return the total number of affected rows
		//Which page is this?
		$paged = !empty($_GET["paged"]) ? addslashes($_GET["paged"]) : '';		
//		$paged = !empty($_GET["paged"]) ? $_GET["paged"] : '';		
		//Page Number
		if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
		//adjust the query to take pagination into account
		if(!empty($paged)){
			$offset=($paged-1)*$this->per_page;
			$query.=' LIMIT '.(int)$offset.','.(int)$this->per_page;
		}

		// Register the columns
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);		   

		// Bulk action
		$this->process_bulk_action();
		
		// Fetch item
		$this->items = $wpdb->get_results($query, 'ARRAY_A');
			
		// Register the pagination
		$current_page = $this->get_pagenum();
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'total_pages' => ceil($total_items/$this->per_page),
			'per_page' => $this->per_page,
		) );		
	}
}