# Midnet Module

## Installation

### Composer.json
	"require" : {
		"zendframework/zend-mvc-plugin-flashmessenger" : "*",
		"zendframework/zend-navigation" : "*",
		"zendframework/zend-i18n" : "*",
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