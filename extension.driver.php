<?php

	Class Extension_Modal_Editing extends Extension{

		public function about(){
			return array('name' => 'Modal Editing',
						 'version' => '0.1',
						 'release-date' => '2011-02-07',
						 'author' => array('name' => 'Nick Dunn',
										   'website' => 'http://nick-dunn.co.uk')
				 		);
		}
		
		public function getSubscribedDelegates(){
			return array(
				array(
					'page' => '/administration/',
					'delegate' => 'AdminPagePreGenerate',
					'callback' => '__appendLightboxJSandCSS'
				),
					
				array(
					'page' => '/administration/',
					'delegate' => 'AdminPagePostGenerate',
					'callback' => '__fixFormAction'
				),				
				
				array(
					'page' => '/publish/new/',
					'delegate' => 'EntryPostCreate',
					'callback' => '__processRedirects'
				),				
				
				array(
					'page' => '/publish/edit/',
					'delegate' => 'EntryPostEdit',
					'callback' => '__processRedirects'
				),
				
				array(
					'page' => '/publish/edit/',
					'delegate' => 'EntryPostDelete',
					'callback' => '__processRedirects'
				),
				
			);
		}
		
		// append CSS to the page head
		public function __appendLightboxJSandCSS($context){
			if(!isset($_GET['lightbox']) || $_GET['lightbox'] != 'true') return;
			Administration::instance()->Page->addStylesheetToHead(URL . '/extensions/modal_editing/assets/modal_editing.publish.css', 'screen', 175);
		}
		
		// modify the form action to add in lightbox and redirects
		public function __fixFormAction($context){
			
			if(!isset($_GET['redirect'])) return;
						
			$pattern = '/<form action="([^"]+)/i';		
			$replacement = '<form action="$1?redirect=' . General::sanitize($_GET['redirect']);
			
			if(isset($_GET['lightbox']) && $_GET['lightbox'] == 'true'){
				$replacement .= '&amp;lightbox=true';
			}
			
			if(isset($_GET['redirect-delete']) && strlen(trim($_GET['redirect-delete'])) > 0){
				$replacement .= '&amp;redirect-delete=' . General::sanitize($_GET['redirect-delete']);
			}
						
			## Add the query into the form action
			$context['output'] = preg_replace(
				$pattern, 
				$replacement, 
				$context['output']
			);
			
		}
		
		// perform the redirects if necessary, to close the lightbox
		public function __processRedirects($context){
			
			if(!isset($_GET['redirect'])) return;
				
			## 1. Check if this is a delete redirect	
			if(isset($_POST['action']['delete']) && isset($_GET['redirect-delete']) && strlen(trim($_GET['redirect-delete'])) > 0){
				$redirect = trim($_GET['redirect-delete']);
			}
			
			else{
				$redirect = trim($_GET['redirect']);
			}
			
			## 2. Form has been submitted
			if(isset($_POST['action'])){	
				
				// Do the replacements
				foreach($_POST['fields'] as $key => $value){
					$redirect = str_replace('{$'.$key.'}', Lang::createHandle($value), $redirect);
				}

				## No lightbox, perform redirection				
				if(!isset($_GET['lightbox'])){
					redirect(URL . $redirect);
				}

				## Lightbox set, redirect to temporary page
				else{
					print '<html><body>
							<script type="text/javascript">
							var redirect = "'.$redirect.'";
							window.parent.location.href = redirect;
							</script>
						</body></html>';
				}
			}
				
		}

	}
