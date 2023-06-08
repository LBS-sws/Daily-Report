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
			'Customer Service Count'=>array(//客戶服務匯總
				'access'=>'A12',
				'url'=>'/serviceCount/index',
			),
			'Customer Service ID'=>array(
				'access'=>'A11',
				'url'=>'/serviceID/index',
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
			'Monthly Report Input'=>array(
				'access'=>'A09',
				'url'=>'/monthly/index',
			),
		),
	),
    'Quality'=>array(
        'access'=>'E',
        'icon'=>'fa-life-ring',
        'items'=>array(
            'Average score of quality inspection'=>array(
                'access'=>'E01',
                'url'=>'/quality/index',
            ),
        ),
    ),
	'Report'=>array(
		'access'=>'B',
		'icon'=>'fa-file-text-o',
		'items'=>array(
			'U Service Amount'=>array( //服務金額匯總
				'access'=>'B32',
				'url'=>'/report/uService',
			),
			'Summary Service Cases'=>array( //客戶服務匯總
				'access'=>'B30',
				'url'=>'/report/summarySC',
			),
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
			'Customer-All'=>array(
				'access'=>'B24',
				'url'=>'/report/custterall',
			),
			'ID-Customer-New'=>array(
				'access'=>'B18',
				'url'=>'/report/customerID?type=N',
			),
			'ID-Customer-Renewal'=>array(
				'access'=>'B19',
				'url'=>'/report/customerID?type=C',
			),
			'ID-Customer-Suspended'=>array(
				'access'=>'B20',
				'url'=>'/report/customerID?type=S',
			),
			'ID-Customer-Resume'=>array(
				'access'=>'B21',
				'url'=>'/report/customerID?type=R',
			),
			'ID-Customer-Amendment'=>array(
				'access'=>'B22',
				'url'=>'/report/customerID?type=A',
			),
			'ID-Customer-Terminate'=>array(
				'access'=>'B23',
				'url'=>'/report/customerID?type=T',
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
			'Active Contract'=>array( 
				'access'=>'B31',
				'url'=>'/report/activeService',
			),
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
            'Comprehensive data comparative analysis'=>array(
                'access'=>'G02',
                'url'=>'/comprehensive/index',
            ),
            'Summary'=>array(
                'access'=>'G03',
                'url'=>'/summary/index',
            ),
            'Comparison'=>array(
                'access'=>'G05',
                'url'=>'/comparison/index',
            ),
            'History Add'=>array(
                'access'=>'G07',
                'url'=>'/historyAdd/index',
            ),
            'History Stop'=>array(
                'access'=>'G08',
                'url'=>'/historyStop/index',
            ),
            'History Net'=>array(
                'access'=>'G09',
                'url'=>'/historyNet/index',
            ),
            'U Service Amount'=>array(
                'access'=>'G10',
                'url'=>'/uService/index',
            ),
            'Sales Analysis'=>array(//销售生产力分析
                'access'=>'G12',
                'url'=>'/salesAnalysis/index',
            ),
            'Average office'=>array(//月预计平均人效
                'access'=>'G13',
                'url'=>'/salesAverage/index',
            ),
            'Lifeline Set'=>array(//生命线设置
                'access'=>'G11',
                'url'=>'/lifeline/index',
            ),
            'Comparison Set'=>array(
                'access'=>'G06',
                'url'=>'/comparisonSet/index',
            ),
            'City Count Set'=>array(//城市统计设置
                'access'=>'G14',
                'url'=>'/citySet/index',
            ),
		),
	),
    'WorkOrder'=>array(
        'access'=>'WO',
        'icon'=>'fa fa-industry',
        'items'=>array(
            'LBS WorkOrder'=>array(
                'access'=>'WO01',
                'url'=>'/worklist/index',
            )
        ),
    ),
    'City Ranking list'=>array(
        'access'=>'T',
		'icon'=>'fa-line-chart',
        'items'=>array(
            'Months Ranking list'=>array(
                'access'=>'T01',
                'url'=>'/rankMonth/index',
            ),
        ),
    ),
    'Monthly Report'=>array(
        'access'=>'H',
		'icon'=>'fa-calendar',
        'items'=>array(
            'Monthly Report Data'=>array(
                'access'=>'H01',
                'url'=>'/month/index',
            ),
            'Monthly Report Analysis'=>array(
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
			'Customer Type ID'=>array(
				'access'=>'C10',
				'url'=>'/customertypeID/index',
				'tag'=>'@',
			),
            'Pay Week'=>array(//付款周期
                'access'=>'C09',
                'url'=>'/payWeek/index',
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
            'Service Endreason'=>array(
                'access'=>'C08',
                'url'=>'/serviceendreason/index',
            ),
            'Stop Remark'=>array(
                'access'=>'C11',
                'url'=>'/stopRemark/index',
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
			'Announcement'=>array(
				'access'=>'D05',
				'url'=>'/announce/index',
				'tag'=>'@',
			),
			'System Log'=>array(
				'access'=>'D06',
				'url'=>'/systemLog/index',
				'tag'=>'@',
			),
		),
	),
);
