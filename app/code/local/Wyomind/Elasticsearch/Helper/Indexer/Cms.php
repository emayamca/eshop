<?php

/**     
 * The technical support is guaranteed for all modules proposed by Wyomind.
 * The below code is obfuscated in order to protect the module's copyright as well as the integrity of the license and of the source code.
 * The support cannot apply if modifications have been made to the original source code (https://www.wyomind.com/terms-and-conditions.html).
 * Nonetheless, Wyomind remains available to answer any question you might have and find the solutions adapted to your needs.
 * Feel free to contact our technical team from your Wyomind account in My account > My tickets. 
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
  class Wyomind_Elasticsearch_Helper_Indexer_Cms extends Wyomind_Elasticsearch_Helper_Indexer_Abstract {public $x18=null;public $xb0=null;public $xa3=null;  protected $_blockClass = 'Wyomind_Elasticsearch_Block_Autocomplete_Cms';  private $x212 = null; public $error = "\105\x6c\x61\163ti\143se\141\162c\150 \103\115\x53\x20I\156d\145x\40\x3a\x20\x49\156\166\x61l\x69\x64\x20\114\x69c\x65\x6es\145\41"; public function __construct() {$xc75 = "\150e\x6cp\145\162";$xec7 = "\x61\x70p";$xd16 = "g\145t\115\157d\145\x6c";$xdb5 = "\147\145tSt\x6f\162e\x43\157\x6efi\147";$xe2d = "\147et\122\x65\x73\157\165r\x63eM\157\144el";$xe7d = "\144\151\163\160\x61\164c\150\x45ve\156t"; $this->_construct(); } public function _construct() {$xc75 = "he\x6cp\145\162";$xec7 = "app";$xd16 = "\147e\x74\x4d\157\144e\x6c";$xdb5 = "\147\145\164\x53\x74o\162e\x43on\x66\x69\147";$xe2d = "\x67e\164\x52\145\x73o\165\x72\143\x65\x4d\157de\154";$xe7d = "\x64\151\x73p\141\x74\143\x68Even\x74"; $this->x212 = Mage::helper("l\x69\143en\163\145\x6d\141\x6e\x61g\x65\162\57\x64\141t\x61"); $this->x212->constructor($this, func_get_args()); }  public function export($xa0 = array()) {$x67 = $this->xa3->x200->{$this->xb0->x200->{$this->x18->x200->{$this->x18->x200->{$this->xb0->x200->xc0f}}}};$x58 = $this->xb0->x200->{$this->x18->x200->{$this->x18->x200->{$this->x18->x200->xc1d}}};$x8f = $this->xb0->x200->{$this->x18->x200->xc2a};$x9a = $this->xb0->x1d6->{$this->x18->x1d6->x7fe};$xd9 = $this->xb0->x1bc->x3a8;$xc75 = "\150\145\x6c\x70\145r";$xec7 = "\141\160p";$xd16 = "g\145\164\x4d\x6fd\145\x6c";$xdb5 = "g\x65\164\123\164\157\x72\145C\x6f\156\146\151\147";$xe2d = "\x67\x65\164\122e\x73o\x75\x72ceMod\145\154";$xe7d = "\x64\151s\160a\164\x63h\x45v\x65\156\164"; try { ${$this->xb0->x1d6->{$this->xb0->x1d6->{$this->x18->x1d6->{$this->xb0->x1d6->{$this->x18->x1d6->x691}}}}} = $this; ${$this->xb0->x200->{$this->x18->x200->xaf8}} = "\115a\147e"; ${$this->x18->x1d6->{$this->xa3->x1d6->{$this->x18->x1d6->{$this->xa3->x1d6->{$this->xa3->x1d6->x6a8}}}}} = "hel\x70\145r"; ${$this->xa3->x200->{$this->xa3->x200->xb07}} = "\164\150\x72\x6f\167Ex\x63\145\160\x74\151\157\x6e"; ${$this->xb0->x1d6->{$this->xb0->x1d6->{$this->x18->x1d6->{$this->xa3->x1d6->x6c6}}}} = $x67($x58()); ${$this->x18->x200->{$this->xa3->x200->{$this->x18->x200->{$this->xa3->x200->xaf2}}}}->{$this->x18->x1bc->{$this->xa3->x1bc->{$this->xb0->x1bc->x217}}}->{$this->x18->x1bc->x404}(${$this->x18->x1bc->{$this->xa3->x1bc->{$this->x18->x1bc->{$this->xa3->x1bc->x23c}}}}, ${$this->xb0->x200->{$this->xa3->x200->xb10}}); if (${$this->x18->x1bc->{$this->xb0->x1bc->x239}}->{$this->xa3->x1bc->x427}(${$this->x18->x1bc->{$this->xb0->x1bc->x25a}}) != $x67(${$this->xa3->x1d6->x6bc})) { ${$this->xa3->x1bc->x242}::${$this->xa3->x200->{$this->xa3->x200->xb07}}(${$this->xa3->x1bc->{$this->xa3->x1bc->{$this->xb0->x1bc->x24a}}}::${$this->xa3->x200->{$this->x18->x200->xafd}}("e\x6c\141\163\x74ic\163e\141\x72c\150")->{$this->x18->x1bc->x436}(${$this->xb0->x1d6->{$this->xb0->x1d6->{$this->x18->x1d6->{$this->xb0->x1d6->{$this->x18->x1d6->x691}}}}}->{$this->x18->x1bc->{$this->x18->x1bc->x223}})); } ${$this->xa3->x1d6->{$this->xa3->x1d6->x6d0}} = array(); foreach (Mage::$xec7()->{$this->x18->x1bc->x44c}() as ${$this->xa3->x1bc->{$this->xb0->x1bc->{$this->xb0->x1bc->x27b}}}) {  if (!${$this->xa3->x1d6->{$this->xa3->x1d6->{$this->xa3->x1d6->{$this->xa3->x1d6->{$this->xa3->x1d6->x6e1}}}}}->{$this->xa3->x1bc->x454}()) { continue; } ${$this->xa3->x1d6->{$this->xb0->x1d6->{$this->xa3->x1d6->x6e9}}} = (int) ${$this->xa3->x200->{$this->x18->x200->{$this->xb0->x200->xb20}}}->{$this->xa3->x1bc->x458}(); if (isset(${$this->xa3->x1d6->{$this->x18->x1d6->{$this->x18->x1d6->{$this->xa3->x1d6->x67a}}}}['store_id'])) { if (!$x8f(${$this->xa3->x1bc->{$this->xa3->x1bc->{$this->xa3->x1bc->x230}}}['store_id'])) { ${$this->x18->x1bc->x229}['store_id'] = array(${$this->xa3->x1d6->{$this->xb0->x1d6->x674}}['store_id']); } if (!$x9a(${$this->xa3->x1bc->{$this->xa3->x1bc->{$this->x18->x1bc->x281}}}, ${$this->xa3->x1bc->{$this->xa3->x1bc->x22e}}['store_id'])) { continue; } } $this->{$this->xb0->x1bc->x465}(' > Exporting CMS pages of store %s', ${$this->xa3->x200->{$this->xa3->x200->xb1f}}->{$this->xb0->x1bc->x473}()); ${$this->xa3->x1bc->{$this->xb0->x1bc->{$this->xb0->x1bc->{$this->xa3->x1bc->x271}}}}[${$this->x18->x200->{$this->x18->x200->xb2c}}] = array();  ${$this->xb0->x200->{$this->x18->x200->xb31}} = $this->{$this->x18->x1bc->x47c}('cms', ${$this->xa3->x1bc->{$this->xb0->x1bc->{$this->xb0->x1bc->x27b}}}); ${$this->x18->x200->{$this->xb0->x200->xb33}} = Mage::$xd16('cms/page')->{$this->xb0->x1bc->x499}() ->{$this->x18->x1bc->x4a3}(${$this->xa3->x1d6->x6d8}->{$this->xa3->x1bc->x458}()) ->{$this->xb0->x1bc->x4c2}(${$this->x18->x1d6->{$this->xa3->x1d6->x6f3}}); if (${$this->x18->x1bc->{$this->x18->x1bc->x2ab}} = $this->{$this->x18->x1bc->{$this->xa3->x1bc->{$this->x18->x1bc->x35b}}}(${$this->xa3->x1d6->{$this->xa3->x1d6->{$this->xa3->x1d6->{$this->xb0->x1d6->x6dc}}}})) { ${$this->xa3->x1bc->{$this->xa3->x1bc->x29c}}->{$this->x18->x1bc->x4e4}('page_id', array('nin' => ${$this->xb0->x1d6->{$this->x18->x1d6->{$this->xa3->x1d6->{$this->xa3->x1d6->x711}}}})); } ${$this->xa3->x1bc->{$this->xa3->x1bc->{$this->xa3->x1bc->{$this->xa3->x1bc->x2a3}}}}->{$this->x18->x1bc->x4e4}('is_active', array('eq' => 1)); foreach (${$this->x18->x200->xb32} as ${$this->xb0->x200->{$this->xa3->x200->xb53}}) { ${$this->xa3->x1bc->{$this->x18->x1bc->x26a}}[${$this->xb0->x1bc->x27e}][${$this->xa3->x1d6->{$this->x18->x1d6->x716}}->{$this->xa3->x1bc->x458}()] = $xd9( array('id' => ${$this->xb0->x1d6->x712}->{$this->xa3->x1bc->x458}()), ${$this->xb0->x200->{$this->x18->x200->{$this->xa3->x200->xb57}}}->{$this->xa3->x1bc->x518}(${$this->xa3->x1d6->x6ee}) ); } $this->{$this->xb0->x1bc->x465}(' > CMS pages exported'); } return ${$this->xa3->x200->xb16}; } catch (Exception $e) { throw $e; } }  public function getExcludedPageIds($xf8 = null) {$xf7 = $this->xa3->x1d6->x812;$xc75 = "\x68\145\154\160er";$xec7 = "\141\160\x70";$xd16 = "\147\145\164\x4dod\x65\x6c";$xdb5 = "\147\x65tSt\157reC\x6f\156\x66\x69g";$xe2d = "\147\145\164R\x65\x73\157\165\162c\x65Mode\x6c";$xe7d = "d\x69\163\160a\x74ch\x45vent"; return $xf7(',', Mage::$xdb5('elasticsearch/cms/excluded_pages', ${$this->xa3->x200->{$this->x18->x200->{$this->xb0->x200->xb6e}}})); }  public function getStoreIndexProperties($x179 = null) {$x11f = $this->xa3->x200->xc4b;$x159 = $this->x18->x1d6->{$this->xa3->x1d6->x831};$x188 = $this->xa3->x1bc->{$this->xb0->x1bc->{$this->xa3->x1bc->{$this->xb0->x1bc->{$this->x18->x1bc->x3e5}}}};$xc75 = "\150\145\154pe\x72";$xec7 = "\x61p\x70";$xd16 = "g\145t\115\157de\x6c";$xdb5 = "\147\145\x74\x53\164\157\x72\x65\103o\x6e\x66\151\147";$xe2d = "g\x65\x74\122e\x73\157\165\162\143\x65\x4dod\x65\154";$xe7d = "d\151\x73\x70a\164\143hE\x76e\x6e\164"; ${$this->xa3->x1d6->x724} = Mage::$xec7()->{$this->xa3->x1bc->x54b}(${$this->x18->x200->xb73}); ${$this->x18->x1bc->x2e5} = 'elasticsearch_cms_index_properties_' . ${$this->xa3->x1d6->x724}->{$this->xa3->x1bc->x458}(); if (Mage::$xec7()->{$this->x18->x1bc->x581}('config')) { ${$this->xb0->x200->{$this->xb0->x200->{$this->xb0->x200->{$this->xb0->x200->xb91}}}} = Mage::$xec7()->{$this->x18->x1bc->x59b}(${$this->xa3->x1d6->x733}); if (${$this->xb0->x1bc->{$this->x18->x1bc->x2f4}}) { return $x11f(${$this->xb0->x1bc->x2f0}); } } ${$this->xa3->x200->{$this->xa3->x200->{$this->x18->x200->{$this->xb0->x200->xb9f}}}} = $this->{$this->xa3->x1bc->x5aa}(${$this->x18->x200->xb73}); ${$this->xb0->x1d6->{$this->xa3->x1d6->{$this->xa3->x1d6->x73b}}} = array(); ${$this->xb0->x1d6->x74e} = Mage::$xe2d('cms/page'); ${$this->x18->x200->xba8} = ${$this->x18->x200->xba1}->{$this->xa3->x1bc->x5c2}()->{$this->x18->x1bc->x5d2}(${$this->xb0->x1bc->{$this->x18->x1bc->{$this->xb0->x1bc->{$this->x18->x1bc->x302}}}}->{$this->x18->x1bc->x5df}()); foreach ($this->{$this->x18->x1bc->x47c}('cms', ${$this->x18->x1d6->{$this->xb0->x1d6->{$this->x18->x1d6->{$this->x18->x1d6->x730}}}}) as ${$this->x18->x200->xbac}) { if (isset(${$this->x18->x200->{$this->x18->x200->xbaa}}[${$this->xa3->x1bc->{$this->x18->x1bc->{$this->xa3->x1bc->{$this->xb0->x1bc->x321}}}}])) { ${$this->x18->x1d6->x736}[${$this->xa3->x1bc->{$this->x18->x1bc->{$this->xa3->x1bc->x31e}}}] = array( 'type' => 'string', 'analyzer' => 'std', 'include_in_all' => true, 'boost' => 1,  'fields' => array( 'std' => array( 'type' => 'string', 'analyzer' => 'std', ) ), ); if (${$this->xb0->x1bc->{$this->xa3->x1bc->{$this->xb0->x1bc->{$this->xa3->x1bc->x311}}}}[${$this->x18->x200->{$this->x18->x200->{$this->xb0->x200->xbb3}}}]['DATA_TYPE'] == Varien_Db_Ddl_Table::TYPE_VARCHAR) { ${$this->xb0->x200->{$this->xb0->x200->{$this->xa3->x200->xb8e}}}[${$this->x18->x200->{$this->xa3->x200->xbb1}}]['fields'] = $x159(${$this->xb0->x200->{$this->xb0->x200->{$this->xa3->x200->xb8e}}}[${$this->xa3->x1d6->{$this->xa3->x1d6->{$this->xb0->x1d6->x768}}}]['fields'], array( 'prefix' => array( 'type' => 'string', 'analyzer' => 'text_prefix', 'search_analyzer' => 'std', ), 'suffix' => array( 'type' => 'string', 'analyzer' => 'text_suffix', 'search_analyzer' => 'std', ), )); } if (isset(${$this->xa3->x200->{$this->xa3->x200->{$this->x18->x200->{$this->xb0->x200->xb9f}}}}['analysis']['analyzer']['language'])) { ${$this->xb0->x1d6->{$this->xa3->x1d6->{$this->xa3->x1d6->{$this->xb0->x1d6->x73d}}}}[${$this->x18->x200->xbac}]['analyzer'] = 'language'; } } } ${$this->x18->x1d6->x736} = new Varien_Object(${$this->xb0->x200->{$this->xb0->x200->{$this->xa3->x200->xb8e}}}); Mage::$xe7d('wyomind_elasticsearch_index_properties', array( 'indexer' => $this, 'store' => ${$this->x18->x1d6->{$this->xb0->x1d6->{$this->x18->x1d6->{$this->x18->x1d6->x730}}}}, 'properties' => ${$this->xb0->x1d6->{$this->xb0->x1d6->x739}}, )); ${$this->xb0->x1bc->x2f0} = ${$this->xb0->x1d6->{$this->xa3->x1d6->{$this->xa3->x1d6->x73b}}}->{$this->xa3->x1bc->x427}(); if (Mage::$xec7()->{$this->x18->x1bc->x581}('config')) { ${$this->xa3->x200->{$this->xb0->x200->{$this->xb0->x200->xbc0}}} = $this->{$this->x18->x1bc->x62b}(); Mage::$xec7()->{$this->xa3->x1bc->x643}($x188(${$this->x18->x1d6->x736}), ${$this->xa3->x1d6->x733}, array('config'), ${$this->xa3->x200->{$this->xb0->x200->{$this->xb0->x200->xbc0}}}); } return ${$this->xb0->x200->{$this->xb0->x200->{$this->xb0->x200->{$this->xb0->x200->{$this->xb0->x200->xb95}}}}}; } } 