<?php

return [
    "admin" => [
        "name"=> "Admin",
        "description"=> "An admin can administer all areas of the site. If a user has this role, they have all other roles, whether selected or not.",
        "permissions"=> [
            "*"
        ]
    ],
    "moderator" => [
        "name" => "Moderator",
        "description" => "A moderator can moderate content, but does not have the full access of an admin.",
        "permissions" => [
            "viewOwnAccount",
            "moderateContent"
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
