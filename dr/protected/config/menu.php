<?php

return array(
	'Data Entry'=>array(
		'access'=>'A',
		'icon'=>'fa-pencil-square-o',
		'items'=>array(
			'Staff Info'=>array(
				'access'=>'A07',
				'url'=>'/staff/index',
			),
			'Customer Info'=>array(
				'access'=>'A01',
				'url'=>'/customer/index',
			),
			'Customer Service'=>array(
				'access'=>'A02',
				'url'=>'/service/index',
			),
			'Complaint Cases'=>array(
				'access'=>'A03',
				'url'=>'/followup/index',
			),
			'Customer Enquiry'=>array(
				'access'=>'A04',
				'url'=>'/enquiry/index',
			),
			'Supplier Info'=>array(
				'access'=>'A10',
				'url'=>'/supplier/index',
			),
			'Product Delivery'=>array(
				'access'=>'A05',
				'url'=>'/logistic/index',
			),
			'QC Record'=>array(
				'access'=>'A06',
				'url'=>'/qc/index',
			),
			'Feedback'=>array(
				'access'=>'A08',
				'url'=>'/feedback/index',
			),
			'Monthly Report Data'=>array(
				'access'=>'A09',
				'url'=>'/monthly/index',
			),
		),
	),
	'Report'=>array(
		'access'=>'B',
		'icon'=>'fa-file-text-o',
		'items'=>array(
			'Complaint Cases'=>array(
				'access'=>'B01',
				'url'=>'/report/complaint',
			),
			'Customer-New'=>array(
				'access'=>'B02',
				'url'=>'/report/custnew',
			),
			'Customer-Renewal'=>array(
				'access'=>'B15',
				'url'=>'/report/custrenew',
			),
			'Customer-Suspended'=>array(
				'access'=>'B03',
				'url'=>'/report/custsuspend',
			),
			'Customer-Resume'=>array(
				'access'=>'B04',
				'url'=>'/report/custresume',
			),
			'Customer-Amendment'=>array(
				'access'=>'B05',
				'url'=>'/report/custamend',
			),
			'Customer-Terminate'=>array(
				'access'=>'B10',
				'url'=>'/report/custterminate',
			),
			'Customer Enquiry'=>array(
				'access'=>'B06',
				'url'=>'/report/enquiry',
			),
			'Product Delivery'=>array(
				'access'=>'B07',
				'url'=>'/report/logistic',
			),
			'QC Record'=>array(
				'access'=>'B08',
				'url'=>'/report/qc',
			),
			'Staff'=>array(
				'access'=>'B09',
				'url'=>'/report/staff',
			),
			'All Daily Reports'=>array(
				'access'=>'B11',
				'url'=>'/report/all',
			),
			'Renewal Reminder'=>array(
				'access'=>'B13',
				'url'=>'/report/renewal',
			),
			'Feedback Statistics'=>array(
				'access'=>'B16',
				'url'=>'/report/feedbackstat',
			),
			'Feedback List'=>array(
				'access'=>'B17',
				'url'=>'/report/feedback',
			),
//			'Monthly Report'=>array(
//				'access'=>'B14',
//				'url'=>'/report/monthly',
//			),
			'Report Manager'=>array(
				'access'=>'B12',
				'url'=>'/queue/index',
			),
		),
	),
	'Management'=>array(
		'access'=>'G',
		'icon'=>'fa-user-secret',
		'items'=>array(
			'LBS Customer Enquiry'=>array(
				'access'=>'G01',
				'url'=>'/customerenq/index',
			),
		),
	),
    '月报表'=>array(
        'access'=>'H',
        'items'=>array(
            'Monthly Report Data'=>array(
                'access'=>'H01',
                'url'=>'/month/index',
            ),
            '月报表分析'=>array(
                'access'=>'H02',
                'url'=>'/mfx/index',
            ),
        ),
    ),
	'System Setting'=>array(
		'access'=>'C',
		'icon'=>'fa-gear',
		'items'=>array(
			'Nature'=>array(
				'access'=>'C01',
				'url'=>'/nature/index',
				'tag'=>'@',
			),
			'Customer Type'=>array(
				'access'=>'C02',
				'url'=>'/customertype/index',
				'tag'=>'@',
			),
//			'Supplier Type'=>array(
//				'access'=>'C08',
//				'url'=>'/suppliertype/index',
//				'tag'=>'@',
//			),
			'Location'=>array(
				'access'=>'C03',
				'url'=>'/location/index',
			),
			'Task'=>array(
				'access'=>'C04',
				'url'=>'/task/index',
			),
			'City'=>array(
				'access'=>'C05',
				'url'=>'/city/index',
				'tag'=>'@',
			),
			'Product'=>array(
				'access'=>'C06',
				'url'=>'/product/index',
				'tag'=>'@',
			),
			'Service Type'=>array(
				'access'=>'C07',
				'url'=>'/servicetype/index',
				'tag'=>'@',
			),
		),
	),
	'Security'=>array(
		'access'=>'D',
		'icon'=>'fa-shield',
		'items'=>array(
			'User'=>array(
				'access'=>'D01',
				'url'=>'/user/index',
				'tag'=>'@',
			),
			'Access Template'=>array(
				'access'=>'D02',
				'url'=>'/group/index',
				'tag'=>'@',
			),
			'Station'=>array(
				'access'=>'D03',
				'url'=>'/station/index',
				'tag'=>'@',
			),
			'Station Register'=>array(
				'access'=>'D04',
				'url'=>'/register/index',
				'tag'=>'@',
			),
		),
	),
);
