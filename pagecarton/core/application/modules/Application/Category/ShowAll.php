<?php
/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @user   Ayoola
 * @package    Application_Category_ShowAll
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: ShowAll.php 5.11.2012 12.02am ayoola $
 */

/**
 * @see Application_Category_Abstract
 */
 
require_once 'Application/Category/Abstract.php';


/**
 * @user   Ayoola
 * @package    Application_Category_ShowAll
 * @copyright  Copyright (c) 2011-2016 PageCarton (http://www.pagecarton.com)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

class Application_Category_ShowAll extends Application_Category_Abstract
{

    /**
     * Access level for player
     *
     * @var boolean
     */
	protected static $_accessLevel = 0;

    /**
     * Post Categories
     *
     * @var array
     */
	protected static $_postCategories;
		
    /**
     * The method does the whole Class Process
     * 
     */
	protected function init()
    {
		$this->setViewContent( '', true );
		
 		//	Choose the kind of categories to show
		@$categoryId = $_GET['category']; 
		if( $this->getParameter( 'ignore_category_query_string' ) )
		{
			// switch $_GET['category'] off for this instance 
			@$categoryId = null; 
		}
		@$categoryId = $this->getParameter( 'category' ) ? : $categoryId;
		@$categoryId = $this->getParameter( 'category_id' ) ? : $categoryId;
		@$categoryId = $this->getParameter( 'category_name' ) ? : $categoryId;
		if( $categoryId )
		{
			switch( gettype($categoryId ) )
			{
				case 'array':
				//	Do nothing
				break;
				case 'string':
					$categoryId = array_map( 'trim', explode( ',', $categoryId ) );
				default:
				break;
			}
			//	Categories
			$table = Application_Category::getInstance();
			
			$children = array();
			if( $categoryInfo = $table->select( null, array( 'category_name' => @$categoryId ) ) )
			{
				//	self::v( $categoryInfo );
				foreach( $categoryInfo as $each )
				{
					$children += (array) $each['child_category_name'];
				}
				
			}
			
			if( $categoryInfo = $table->select( null, array( 'parent_category_name' => $categoryId ) ) )
			{
				//	self::v( $categoryInfo );
				foreach( $categoryInfo as $each )
				{
					$children[] = $each['category_name'];
				}
			}
			$this->_dbWhereClause['category_name'] = $children;

		}
		//	switch templates off
	//	$this->_parameter['markup_template'] = null; 
		$data = $this->getDbData();
		krsort( $data );
		if( $data )
		//while( $this->getDbData() )
		{
		//	$data = array_shift( $this->getDbData() );
			$dataToPad = array( 
						//	'article_url' => 'javascript:',   
							'allow_raw_data' => true, 
							'article_type' => 'category_information', 
						//	'document_url' => 'http://placehold.it/300x300&text=No Picture',
							'publish' => true, 
							'auth_level' => 0, 
							'article_title' => '', 
							'article_description' => '', 
						);
		//	self::v( $data );
			//	Use article viewer to bring out this
			$parameters = array_merge( $this->getParameter() ? : array(), array( 'data_to_merge' => $dataToPad, 'get_access_information' => true ) );
		//	self::v( $parameters );
			$class = new Application_Article_ShowAll( array( 'no_init' => true ) );
			$class->setDbData( $data );
			$class->setParameter( $parameters );
			$class->init();
		//	$this->setViewContent( $class->view() );
		
			$parameters = $class->getParameter();		
			unset( $parameters['markup_template_no_data'] );
			
			//	return parameters
			$this->setParameter( $parameters );
	//		self::v( $this->getParameter( 'markup_template' ) );
/* 			$this->_parameter['markup_template'] = $class->getParameter( 'markup_template' );
			$this->_parameter['markup_template_suffix'] = null;
			$this->_parameter['markup_template_prefix'] = null;
 */			$this->_objectTemplateValues = $class->getObjectTemplateValues();
			//	break;
		}
	//	$this->setViewContent( '', true );
    } 

     /**
     * This method returns the _classOptions property 
     *
     * @param void
     * @return array
     */
    public static function getPostCategories()
    {
		if( null === self::$_postCategories )
		{
			//	Defaults to all categories available
			$articleSettings = Application_Article_Settings::getSettings( 'Articles' );
			$options = new Application_Category;
	////		var_export( $articleSettings );
			if( $articleSettings['allowed_categories'] )
			{
				if( ! self::$_postCategories = $options->select( null, array( 'category_name' => $articleSettings['allowed_categories'] ) ) )
				{
					self::$_postCategories = array();
				}
			}
			else
			{
				if( ! self::$_postCategories = $options->select() )
				{
					self::$_postCategories = array();
				}
			}
		//	self::$_postCategories$options;

		}
		return self::$_postCategories;
	}
	// END OF CLASS
}
