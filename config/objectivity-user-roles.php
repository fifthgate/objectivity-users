<?php

return [
	"admin" => [
		"name"=> "Admin",
		"description"=> "An admin can administer all areas of the site. If a user has this role, they have all other roles, whether selected or not.",
		"permissions"=> [
			"*"
		]
	],
	"registered-user" => [
		"name"=> "Registered User",
		"description"=> "A Registered User can view pages, and their own account",
		"permissions"=> [
			"viewOwnAccount",
		]
	],
];