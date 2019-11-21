# Midnet Module

## Installation

### Composer.json
	"require" : {
		"zendframework/zend-mvc-plugin-flashmessenger" : "*",
		"zendframework/zend-navigation" : "*",
		"zendframework/zend-i18n" : "*",
		"zendframework/zend-db" : "*",
		"zendframework/zend-form" : "2.12.0",
		"zendframework/zend-paginator" : "*"
	}, 
	"autoload" : {
		"psr-4" : {
			"Midnet\\" : "module/Midnet/src",
		},
	},

### modules.config.php
	return [
		'Midnet',
	];