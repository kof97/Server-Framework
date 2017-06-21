<?php

$json = '{
	"module": {
		"name": "demo",
		"description": "The demo",
		"types": {
			"user_list": {
				"description": "",
				"element": {
					"id": { "type": "integer", "require": "yes" },
					"user_id": { "type": "user_id", "require": "yes" },
					"user_name": { "type": "user_name", "require": "yes" }
				}
			}
		},
		"interface": {
			"read": {
				"description": "",
				"method": "GET",
				"request": {
					"id": { "type": "user_id", "require": "yes" },
					"name": { "type": "user_name", "require": "yes" }
				},
				"response": {
					"list": { "type": "user_list" }
				}
			},
			"create": {
				"description": "",
				"method": "POST",
				"request": {

				},
				"response": {

				}
			}
		}
	}
}';

$res = json_decode($json, true);


var_dump($res);





// end of script
