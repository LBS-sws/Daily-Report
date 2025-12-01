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
			'Customer Service KA'=>array(
				'access'=>'A13',
				'url'=>'/serviceKA/index',
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
    'Cross dispatch'=>array(//交叉派单
        'access'=>'CW',
        'icon'=>'fa-superpowers',
        'items'=>array(
            'Cross Apply'=>array(//交叉派单申请
                'access'=>'CW01',
                'url'=>'/crossApply/index',
            ),
            'Cross Audit'=>array(//交叉派单审核
                'access'=>'CW02',
                'url'=>'/crossAudit/index',
            ),
            'Cross Search'=>array(//交叉派单查询
                'access'=>'CW03',
                'url'=>'/crossSearch/index',
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
            'KA Retention report'=>array( //KA保留率详情报表
                'access'=>'B41',
                'url'=>'/report/kaRetention',
            ),
			'KA signed report'=>array( //KA新签报表
				'access'=>'B40',
				'url'=>'/report/kaSigned',
			),
			'Cross report'=>array( //交叉派单报表
				'access'=>'B39',
				'url'=>'/report/cross',
			),
			'service loss report'=>array( //合约暂停或丢失
				'access'=>'B38',
				'url'=>'/report/serviceLoss',
			),
			'Supplier report'=>array( //供应商资料报表
				'access'=>'B37',
				'url'=>'/report/supplier',
			),
			'Contract comparison report'=>array( //合约对比报表
				'access'=>'B36',
				'url'=>'/report/contractCom',
			),
			'KA customer report'=>array( //ke客户报表
				'access'=>'B34',
				'url'=>'/report/customerKA',
			),
			'All customer report'=>array( //客户记录汇总表
				'access'=>'B42',
				'url'=>'/report/customerAll',
			),
			'Chain customer report'=>array( //连锁客户报表
				'access'=>'B33',
				'url'=>'/report/chain',
			),
			'U Service Amount'=>array( //技术员生产力分析
                'access'=>'B32',
				'url'=>'/report/uService',
			),
			'U Service Detail'=>array( //U系统服务报表
                'access'=>'B35',
				'url'=>'/report/uServiceDetail',
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
	'Management Bonus'=>array(//管理层月度奖金计算
		'access'=>'MM',
		'icon'=>'fa-user-secret',
		'items'=>array(
			'Management Month Bonus'=>array(//管理层月度奖金计算
				'access'=>'MM01',
				'url'=>'/manageMonthBonus/index',
			),
			'Management Staff Setting'=>array(//管理层人员设置
				'access'=>'MM02',
				'url'=>'/manageStaffSet/index',
			),
			'Management Stop Setting'=>array(//停单率提成设置
				'access'=>'MM03',
				'url'=>'/manageStopSet/index',
			),
        )
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
            'Update Service Count'=>array( //客户服务修改统计
                'access'=>'G34',
                'url'=>'/updateSeCount/index',
            ),
            'Check Week Staff'=>array( //周人效
                'access'=>'G38',
                'url'=>'/checkStaffWeek/index',
            ),
            'Check In Month'=>array( //签到签离月统计
                'access'=>'G37',
                'url'=>'/checkInMonth/index',
            ),
            'Check In Week'=>array( //签到签离周统计
                'access'=>'G36',
                'url'=>'/checkInWeek/index',
            ),
            'Check In Staff'=>array( //签到签离员工统计
                'access'=>'G35',
                'url'=>'/checkInStaff/index',
            ),
            'KA Tracking'=>array( //KA業績追蹤
                'access'=>'G33',
                'url'=>'/kATrack/index',
            ),
            'Retention rate'=>array( //保留率
                'access'=>'G30',
                'url'=>'/retentionRate/index',
            ),
            'Retention KA rate'=>array( //KA保留率
                'access'=>'G31',
                'url'=>'/retentionKARate/index',
            ),
            'Outsource'=>array( //外包数据分析
                'access'=>'G28',
                'url'=>'/outsource/index',
            ),
            'Out Business'=>array( //业务承揽数据分析
                'access'=>'G39',
                'url'=>'/outBusiness/index',
            ),
            'Bonus Month'=>array( //月度提成奖金表
                'access'=>'G24',
                'url'=>'/bonusMonth/index',
            ),
            'Summary'=>array( //合同分析查询
                'access'=>'G03',
                'url'=>'/summary/index',
            ),
            'KA Summary'=>array( //合同分析查询
                'access'=>'G25',
                'url'=>'/summaryKA/index',
            ),
            'Comparison'=>array( //合同同比分析
                'access'=>'G05',
                'url'=>'/comparison/index',
            ),
            'KA Comparison'=>array( //合同同比分析
                'access'=>'G26',
                'url'=>'/comparisonKA/index',
            ),
            'Monthly performance'=>array( //月度业绩
                'access'=>'G17',
                'url'=>'/perMonth/index',
            ),
            'Week Service U'=>array( //周服务生意额
                'access'=>'G18',
                'url'=>'/weekServiceU/index',
            ),
            'Month Service U'=>array( //月服务生意额
                'access'=>'G22',
                'url'=>'/monthServiceU/index',
            ),
            'Lost orders rate'=>array( //每月丢单率
                'access'=>'G19',
                'url'=>'/lostOrder/index',
            ),
            'Capacity Count'=>array( //产能统计
                'access'=>'G21',
                'url'=>'/capacityCount/index',
            ),
            'History Add'=>array(
                'access'=>'G07',
                'url'=>'/historyAdd/index',
            ),
            'History Stop'=>array(
                'access'=>'G08',
                'url'=>'/historyStop/index',
            ),
            'History Pause'=>array(
                'access'=>'G15',
                'url'=>'/historyPause/index',
            ),
            'History Resume'=>array(
                'access'=>'G16',
                'url'=>'/historyResume/index',
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
            'Sales Month Count'=>array( //每月销售人数统计
                'access'=>'G20',
                'url'=>'/salesMonthCount/index',
            ),
            'Sales productivity'=>array(//销售月均生产率
                'access'=>'G23',
                'url'=>'/salesProd/index',
            ),
            'Average office'=>array(//月预计平均人效
                'access'=>'G13',
                'url'=>'/salesAverage/index',
            ),
            'Insect and clean'=>array(//全国虫控、清洁停单率
                'access'=>'G29',
                'url'=>'/InsetClean/index',
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
            'City Track Set'=>array(//城市统计设置
                'access'=>'G32',
                'url'=>'/cityTrack/index',
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
            ),
            'Evaluation Statistics'=>array(
                'access'=>'WO02',
                'url'=>'/worklist/evaluation',
            ),
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
            'Pest Type Setting'=>array(
                'access'=>'C12',
                'url'=>'/pestType/index',
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
			'Api Curl'=>array(
				'access'=>'D07',
				'url'=>'/CurlNotes/index',
				'tag'=>'@',
			),
		),
	),
);

