<?php
return [
	'all' => [
		'add',
		'edit',
		'delete',
		'import',
		'export'
	],
	'custom' => [
		// Primary Key are roles e.g user, admin & etc
		'##PRIMARYKEY##'=> [
			// Just add the key like mention in full permission e.g (add, edit, delete, import, export) & etc
			'##KEY##'=> [
			],
		],
	],
	'full' => [
		'audit' => 'all',
		'refurb' => 'all',
		'supply' => 'all',
		'outbound' => 'all',
		'inventory' => 'all',
		'session' => 'all',
		'timetracker' => 'all',
		'recycle' => 'all',
		'recycletwo' => 'all',
		'import' => 'all',
		'wipereport' => 'all',
		'runningsupply' => 'all',
		'user' => 'all',
		'messagelog' => 'all',
	],
	'admin' => [
		'admin' => 'true',
	],
	'roles' => [
		'user' => 'User',
		'manager-itamg' => 'Manager Itamg',
		'manager-refurbconnect' => 'Manager Refurbconnect'
	],
	'rolecapabilities' => [
		'admin' => [
			'admin'
		],
		'manager-itamg' => [
			'full'
		],
		'manager-refurbconnect' => [
			'full'
		],
		'user' => [
			'full'
		]
	],
];
?>
