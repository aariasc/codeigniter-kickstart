<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base controller class
 * All controllers of the application will inherit from it, and it contains
 * shared and common stuff, like page title, keywords, if the page is private
 * and so on.
 * @author: aarias
 */
class MY_Controller extends CI_Controller{

  //Data for all views. Use $this->data in child classes
  protected $data = Array();

  //Could be useful for debug info
  protected $pageName = FALSE;

  //Set to true if the site is private (e.g. requires authentication)
  protected $isPrivatePage = FALSE;

  //Page Metadata.  The idea is to be overwritten by any controller
  protected $title = FALSE;
  protected $description = FALSE;
  protected $keywords = FALSE;
  protected $author = FALSE;

  //Static resources.  A controller can load custom JS, CSS or Google Font
  protected $javascript = array();
  protected $css = array();
  protected $fonts = array();


  // Base class constructur
  function __construct()
  {
    parent::__construct();

    //Useful for pagination and similar plugins
    $this->data["uri_segment_1"] = $this->uri->segment(1);
    $this->data["uri_segment_2"] = $this->uri->segment(2);

    //Get the page name (could be useful for debugging)
    $this->pageName = strToLower(get_class($this));

    //Load page metadata with default values
    //check config/constants.php
    $this->title = SITE_DEFAULT_TITLE;
    $this->description = SEO_META_DESCRIPTION;
    $this->keywords = SEO_META_KEYWORDS;
    $this->author = SEO_AUTHOR;
  }

  // This method will render any given $view inside the main site template
  // The idea is that each view has the important content to be displayed,
  // without extra stuff, like header, menu and so on
  protected function _render($view)
  {
    if($this->isPrivatePage){
      //Validate session data
      if(!$this->session->userdata(SESSION_PUBLIC_SITE)){
        redirect('/', 'refresh');
      }
    }
    //If session validation went fine, load stuff

    //Static resources (extra CSS, JS and Google Fonts)
    $template_data["javascript"] = $this->javascript;
    $template_data["css"] = $this->css;
    $template_data["fonts"] = $this->fonts;

    //Fill SEO metadata and page title
    $template_data["title"] = $this->title;
    $template_data["meta_description"] = $this->description;
    $template_data["meta_keywords"] = $this->keywords;
    $template_data["author"] = $this->author;

    //Load base js and css
    $template_data["basejs"] = $this->load->view("template/base_js",$this->data,true);
    $template_data["basecss"] = $this->load->view("template/base_css",$this->data,true);

    //Load the specific view, with its specific $data map, combined with the template's
    $body_data["body"] = $this->load->view($view,array_merge($this->data,$template_data),true);

    //Header (e.g. navigation)
    $body_data["header"] = $this->load->view("template/header",array_merge($this->data,$template_data),true);

    //Footer (e.g. copyright info or so on)
    $body_data["footer"] = $this->load->view("template/footer",array_merge($this->data,$template_data),true);

    $this->load->view("template/base_template",array_merge($this->data,$body_data));
  }
}
