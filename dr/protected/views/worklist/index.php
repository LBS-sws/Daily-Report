<?php
$this->pageTitle = Yii::app()->name . ' - Riskrank';
?>

<?php
//if (1) {
//    $file_url = Yii::app()->createAbsoluteUrl('statement/test',array('fid'=>1233,'test'=>123));
//    $js = "$(location).attr('href','$file_url');";
//    Yii::app()->clientScript->registerScript('redirection',$js,CClientScript::POS_READY);
//}
//?>
<?php $form = $this->beginWidget('TbActiveForm', array(
    'id' => 'WorkOrder',
    'enableClientValidation' => false,
    'clientOptions' => array('validateOnSubmit' => false,),
    'layout' => TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content" id="app">
    <div class="box">
        <div class="box-body">
            <div>
                <div>
                    <span class="demonstration">日期：</span>
                    <el-date-picker
                            v-model="date1"
                            type="daterange"
                            align="left"
                            unlink-panels
                            range-separator="至"
                            start-placeholder="开始日期"
                            end-placeholder="结束日期"
                            :picker-options="pickerOptions">
                    </el-date-picker>
                    <!--                    </div>-->


                    城市选择：
                    <el-select style="width:120px" v-model="value1" placeholder="请选择" @change="GetCurId1">
                        <el-option
                                v-for="(item, index) in options1"
                                :key="index"
                                :label="item.Text"
                                :value="item.City">
                        </el-option>
                    </el-select>

                    人员选择：
                    <el-select style="width:120px" clearable v-model="value2" placeholder="请选择" @change="GetCurId2">
                        <el-option
                                v-for="(item, index)  in options2"
                                :key="index"
                                :label="item.StaffName"
                                :value="item.StaffID">
                        </el-option>
                    </el-select>


                </div>
                <div style="display: block;padding-top: 10px">

                    类型：
                    <el-select style="width:100px" v-model="value" placeholder="请选择">
                        <el-option
                                v-for="item in options"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value">
                        </el-option>
                    </el-select>

                    <span style="color: #5FCA71">大于时间</span>
                    <el-time-select style="width:150px"
                                    v-model="timeInterval"
                                    :picker-options="{start: '00:00',step: '00:10',end: '23:59'}"
                                    placeholder="时间间隔">
                    </el-time-select>

                    <span style="color: #EB5851">小于时间</span>
                    <el-time-select style="width:150px"
                                    v-model="timeInterval1"
                                    :picker-options="{start: '00:00',step: '00:10',end: '23:59'}"
                                    placeholder="时间间隔">
                    </el-time-select>

                    </span>
                    <el-button style="margin-left: 20px" type="primary" @click="doSearch">查询</el-button>
                    <!--                    <el-button style="margin-left: 20px" type="primary" @click="exportAreaDataexportAreaData">导出</el-button>-->
                </div>
            </div>
        </div>
    </div>
    <el-main v-loading="loading" v-if="showEchars">

        <div id="main" style="width: 600px;height:400px;"></div>
        <div style="float: right">
            <el-button v-if="show_export" type="primary" @click="exportAreaData">下载报表</el-button>
        </div>
    </el-main>

    <!--        <el-button type="text" @click="dialogTableVisible = true">打开嵌套表格的 Dialog</el-button>-->

    <el-dialog v-loading="loading" title="明细" :visible.sync="dialogTableVisible">
        <el-tag style="float: right" type="success" @click="exportData">导出</el-tag>

        <el-table :data="gridData">
            <el-table-column property="job_date" label="排单日期" width="auto"></el-table-column>
            <el-table-column property="finish_date" label="离店日期" width="auto"></el-table-column>
            <el-table-column property="city_name" label="地区" width="auto"></el-table-column>
            <el-table-column property="customer_name" label="客户" width="200px"></el-table-column>
            <el-table-column property="staff_name" label="工作人员" width="auto"></el-table-column>
            <el-table-column property="service_type" label="服务类别" width="auto"></el-table-column>
            <el-table-column property="start_time" label="开始时间" width="auto"></el-table-column>
            <el-table-column property="end_time" label="结束时间" width="auto"></el-table-column>
            <el-table-column property="job_time" label="工作时间"></el-table-column>


            <el-table-column prop="tag" label="状态">
                <template slot-scope="scope">
                    <el-tag :type="'success'" v-if="scope.row.flag == 0">正常</el-tag>
                    <el-tag :type="'warning'" v-if="scope.row.flag == 1">异常</el-tag>
                </template>
            </el-table-column>

        </el-table>
    </el-dialog>


    <div class="box-body table-responsive">
        <!--            <el-button @click="exportExcel('tab1', '会员明细.xlsx')">导出</el-button>-->
        <el-table
                v-loading="loading"
                :data="tableData"
                :default-sort="{prop: 'FinishDate', order: 'descending'}"
                row-key="staff_id"
                show-summary
        //合计方法
        :summary-method="getSummaries" //自定义计算合计
        @row-click="handleclick"
        style="width: 100%">
        <el-table-column
                prop="city_name"
                label="地区"
                width="auto">
        </el-table-column>

        <el-table-column
                prop="staff_name"
                label="姓名"
                width="auto">
        </el-table-column>

        <el-table-column
                prop="total"
                label="总单数"
                width="auto">
        </el-table-column>


        <el-table-column
                prop="normal"
                label="正常单"
                width="auto">
        </el-table-column>

        <el-table-column
                prop="unusual"
                label="异常单"
                width="auto">
        </el-table-column>
        </el-table>
    </div>
    </div>


</section>

<?php $this->endWidget(); ?>


<form class="form-horizontal MultiFile-intercepted" action="" method="post">
    <!--    --><?php //$this->renderPartial('//materiallist/redeemApply',array(
    //        'submit'=> Yii::app()->createUrl('materiallist/apply'),
    //    ));
    //    ?>
</form>
<script src="./../../../js/vue.js"></script>
<script src="./../../../js/element.js"></script>
<script src="./../../../js/echarts.js"></script>
<script src="./../../../js/xlsx.core.min.js"></script>

<!-- 引入样式 -->
<link rel="stylesheet" href="./../../../css/element-ui.css">

<!-- 引入组件库 -->
<!---->


<script>

    new Vue({
        el: '#app',
        data() {
            return {
                dialogTableVisible: false,
                showEchars: false,
                timeInterval: '00:10',
                timeInterval1: '00:00',
                timeIntervalStart: '00:00',
                timeIntervalEnd: '00:00',
                loading: true,
                date1: [],
                options1: [],
                options2: [],
                mark_exception: true,
                is_mark: 1,
                checkUser: '',
                city: '',
                echars_name: '',
                echars_name_copy: '',
                value1: [],
                show_export: false,
                value2: [],
                switch_value: true,
                tableData: [],
                click_staff:'',
                link: '',
                options: [{
                    value: '1',
                    // default:'1',
                    label: '工作单'
                }, {
                    value: '2',
                    label: '跟进单'
                }], value: ''
                , gridData: [],
                pickerOptions: {
                    shortcuts: [{
                        text: '最近一周',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '最近一个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '最近三个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                            picker.$emit('pick', [start, end]);
                        }
                    }]
                },
            };
        },
        watch: {
            options2: function (newValue) {
                this.value2 = [];
                this.checkUser = '';
                this.options2 = newValue;
            },
            timeIntervalStart: function (newValue) {
                this.timeIntervalEnd = newValue
                // console.log(newValue)
            }
        },
        computed: {},
        methods: {
            getXiaoji(name) {
                var sum = 0;
                this.tableData.forEach((n, i) => {
                    sum += parseFloat(n[name]);
                })
                return sum;
            },
            // 表格总计的时间
            getSummaries(param) {
                const {columns, data} = param;
                const sums = [];
                columns.forEach((column, index) => {
                    if (index === 0) {
                        sums[index] = "总计";
                        //	index 表示表格的第几列开始计算
                    } else if (index >= 2) {
                        const values = data.map((item) => Number(item[column.property]));
                        if (!values.every((value) => isNaN(value))) {
                            sums[index] = values.reduce((prev, curr) => {
                                const value = Number(curr);
                                if (!isNaN(value)) {
                                    return Number(prev.toFixed(2)) + curr;
                                } else {
                                    return prev;
                                }
                            }, 0);
                        } else {
                            sums[index] = "N/A";
                        }
                    } else {
                        sums[index] = "--";
                    }
                });
                return sums;
            },

            exportData() {
                var aoa = [["排单日期","离店日期", "地区", "客户", "工作人员", "服务类别", "开始时间", "结束时间", "工作时间", "状态"]];
                this.gridData.map(item => {
                    aoa.push([item.job_date,item.finish_date??'', item.city_name, item.customer_name, item.staff_name, item.service_type, item.start_time, item.end_time, item.job_time, item.status])
                })
                console.log(aoa)
                var sheet = XLSX.utils.aoa_to_sheet(aoa);
                let orgin_time = this.date1;
                let start_date = this.formatDate(orgin_time[0],1);
                let end_date = this.formatDate(orgin_time[1],1);
                this.openDownloadDialog(this.sheet2blob(sheet), '【'+this.click_staff+'】'+start_date+'--'+end_date+'.xlsx');
            },
            // 将一个sheet转成最终的excel文件的blob对象，然后利用URL.createObjectURL下载
            sheet2blob(sheet, sheetName) {
                sheetName = sheetName || 'sheet1';
                var workbook = {
                    SheetNames: [sheetName],
                    Sheets: {}
                };
                workbook.Sheets[sheetName] = sheet;
                // 生成excel的配置项
                var wopts = {
                    bookType: 'xlsx', // 要生成的文件类型
                    bookSST: false, // 是否生成Shared String Table，官方解释是，如果开启生成速度会下降，但在低版本IOS设备上有更好的兼容性
                    type: 'binary'
                };
                var wbout = XLSX.write(workbook, wopts);
                var blob = new Blob([s2ab(wbout)], {type: "application/octet-stream"});

                // 字符串转ArrayBuffer
                function s2ab(s) {
                    var buf = new ArrayBuffer(s.length);
                    var view = new Uint8Array(buf);
                    for (var i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
                    return buf;
                }

                return blob;
            },
            /**
             * 通用的打开下载对话框方法，没有测试过具体兼容性
             * @param url 下载地址，也可以是一个blob对象，必选
             * @param saveName 保存文件名，可选
             */
            openDownloadDialog(url, saveName) {
                if (typeof url == 'object' && url instanceof Blob) {
                    url = URL.createObjectURL(url); // 创建blob地址
                }
                var aLink = document.createElement('a');
                aLink.href = url;
                aLink.download = saveName || ''; // HTML5新增的属性，指定保存文件名，可以不要后缀，注意，file:///模式下不会生效
                var event;
                if (window.MouseEvent) event = new MouseEvent('click');
                else {
                    event = document.createEvent('MouseEvents');
                    event.initMouseEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
                }
                aLink.dispatchEvent(event);
            },


            filterTag(value, row) {
                return row.second === value;
            },
            defaultDate() {
                let date = new Date()
                console.log(date);
                // 通过时间戳计算
                let defalutStartTime = date.getTime() - 1000 * 24 * 3600 * 7 // 转化为时间戳
                let defalutEndTime = date.getTime()
                let startDateNs = new Date(defalutStartTime)
                let endDateNs = new Date(defalutEndTime)
                // 月，日 不够10补0
                defalutStartTime = startDateNs.getFullYear() + '-' + ((startDateNs.getMonth() + 1) >= 10 ? (startDateNs.getMonth() + 1) : '0' + (startDateNs.getMonth() + 1)) + '-' + (startDateNs.getDate() >= 10 ? startDateNs.getDate() : '0' + startDateNs.getDate())
                defalutEndTime = endDateNs.getFullYear() + '-' + ((endDateNs.getMonth() + 1) >= 10 ? (endDateNs.getMonth() + 1) : '0' + (endDateNs.getMonth() + 1)) + '-' + (endDateNs.getDate() >= 10 ? endDateNs.getDate() : '0' + endDateNs.getDate())

                this.date1 = [defalutStartTime, defalutEndTime]
            },
            handleclick(row, column, event,) {
                console.log(row.staff_id);
                this.getStaffInfo(row.staff_id);
                this.dialogTableVisible = true
            },

            drawEchars(echarsData) {
                console.log(echarsData)
                // 基于准备好的dom，初始化echarts实例
                var myChart = echarts.init(document.getElementById('main'));

                // 指定图表的配置项和数据
                var option = {
                    title: {
                        text: '区域异常单统计概览',
                        subtext: this.echars_name,
                        left: 'center'
                    },
                    tooltip: {
                        trigger: 'item'
                    },
                    legend: {
                        orient: 'vertical',
                        left: 'left'
                    },
                    series: [
                        {
                            name: this.echars_name,
                            type: 'pie',
                            radius: '50%',
                            data: echarsData,
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(173,23,23,0.5)'
                                }
                            },
                            itemStyle: {
                                normal: {
                                    label: {
                                        show: true,
                                        positiong: 'top',
                                        // formatter: '{c}%'
                                        formatter: '{b}: {c}({d}%)' //自定义显示格式(b:name, c:value, d:百分比)
                                    }
                                }
                            }
                        }
                    ]
                };
                // 使用刚指定的配置项和数据显示图表。
                myChart.setOption(option);
            },
            doSearch() {
                // this.drawEchars()
                let orgin_time = this.date1;
                if (!this.date1 || this.date1 == 'undefind') {
                    this.$message({
                        message: '未选择时间',
                        type: 'warning'
                    });
                    return;
                }

                if (!this.city || this.city == 'undefind') {
                    this.$message({
                        message: '请选择城市',
                        type: 'warning'
                    });
                    return;
                }

                let start_date = this.formatDate(orgin_time[0],0);
                let end_date = this.formatDate(orgin_time[1],0);
                let city = this.city;
                let url_params = '';
                // if (this.switch_value === true) {
                //     this.is_mark = 1;
                // } else {
                //     this.is_mark = 0;
                // }
                if ((this.timeInterval == '00:00' && this.timeInterval1 == '00:00') || this.timeInterval == '' && this.timeInterval1 == '') {
                    this.$message({
                        message: '至少选择一个时间段作为筛选条件',
                        type: 'warning'
                    });
                    return;
                }
                if (this.timeInterval1 >= this.timeInterval) {
                    this.$message({
                        message: '时间条件筛选有误',
                        type: 'warning'
                    });
                    return;
                }

                url_params = '?start_date=' + start_date + '&end_date=' + end_date + '&staff=' + this.checkUser + '&city=' + city + '&time_point_start=' + this.timeInterval + '&time_point_end=' + this.timeInterval1 + '&service_type=' + this.value + '&is_mark=' + this.is_mark
                this.loading = true
                fetch("./../worklist/jobList" + url_params, {
                    method: "get",
                    // body: JSON.stringify({City:val}),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(result => {
                    // console.log(result);
                    return result.json();
                }).then(res => {
                    if (res.code == 1) {
                        setTimeout(() => {
                            this.loading = false
                        }, 100);
                        this.tableData = res.data.data
                        this.showEchars = true
                        this.echars_name = this.echars_name_copy
                        if (this.checkUser != '' && this.checkUser != 'undefined') {
                            this.echars_name = res.data.data[0].staff_name
                        }
                        this.$nextTick(() => {
                            this.drawEchars(res.data.count) // 渲染操作
                        })

                    } else {
                        this.tableData = []
                        this.showEchars = false
                        setTimeout(() => {
                            this.loading = false
                        }, 100);
                        // this.$nextTick(() => {
                        //     this.drawEchars([]) // 渲染操作
                        // })
                        this.$message.error(res.msg);
                    }
                })
            },
            /**
             * 格式化时间
             * */
            formatDate(d,type = 0) {
                var date = new Date(d);
                var YY = date.getFullYear() + '-';
                var MM =
                    (date.getMonth() + 1 < 10
                        ? '0' + (date.getMonth() + 1)
                        : date.getMonth() + 1) + '-';
                var DD = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
                var hh =
                    (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
                var mm =
                    (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) +
                    ':';
                var ss =
                    date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds();
                var result;
                if(type == 1){
                    result = YY + MM + DD;
                }else{
                    result = YY + MM + DD + ' ' + hh + mm + ss;
                }
                return result;
            },
            GetCurId1(val) {
                this.city = val
                // this.options2.value = null;
                console.log("当前城市为：" + val)
                fetch("./../worklist/staff?city=" + val, {
                    method: "get",
                    // body: JSON.stringify({City:val}),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(result => {
                    // console.log(result);
                    return result.json();
                }).then(res => {
                    // console.log(data);
                    if (res.code == 1) {
                        this.options2 = res.data;
                        this.echars_name = res.data[0].Text;
                        this.echars_name_copy = res.data[0].Text;
                    } else {
                        this.$message({
                            message: '暂无数据',
                            type: 'warning'
                        });
                        this.value2 = []
                        this.options2 = [];
                        this.showEchars = false
                        this.tableData = []
                        setTimeout(() => {
                            this.loading = false
                        }, 100);
                    }

                })
            },
            GetCurId2(val) {
                console.log('GetCurId2')
                console.log(val)
                if (this.options2.length > 0) {
                    console.log(this.options2.length)
                    this.checkUser = val
                } else {
                    this.checkUser = ''
                }

            },
            getStaffInfo(val) {
                // consol
                // this.nowUser = val
                let orgin_time = this.date1;
                let start_date = this.formatDate(orgin_time[0],0);
                let end_date = this.formatDate(orgin_time[1],0);
                if (this.switch_value === true) {
                    this.is_mark = 1;
                } else {
                    this.is_mark = 0;
                }
                this.loading = true

                fetch('./../worklist/StaffInfo?staff_id=' + val + '&start_date=' + start_date + '&end_date=' + end_date + '&time_point_start=' + this.timeInterval + '&time_point_end=' + this.timeInterval1 + '&service_type=' + this.value + '&city=' + this.city + '&is_mark=' + this.is_mark, {
                    method: "get",
                    // body: JSON.stringify({City:val}),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(result => {
                    // console.log(result);
                    return result.json();
                }).then(res => {
                    if (res.code == 1) {
                        this.gridData = res.data;
                        // console.log(res.data[0].staff_name);
                       this.click_staff =  res.data[0].staff_name
                        setTimeout(() => {
                            this.loading = false
                        }, 100);
                    } else {
                        this.gridData = []
                        this.$message({
                            message: '暂无数据',
                            type: 'warning'
                        });
                        setTimeout(() => {
                            this.loading = false
                        }, 100);
                    }
                })
                // this.checkUser = ''
            },

            exportAreaData() {
                let orgin_time = this.date1;
                let start_date = this.formatDate(orgin_time[0],0);
                let end_date = this.formatDate(orgin_time[1],0);
                if (this.switch_value === true) {
                    this.is_mark = 1;
                } else {
                    this.is_mark = 0;
                }
                this.loading = true
                if (orgin_time == '' || orgin_time == undefined || this.city == '') {
                    this.$message({
                        message: '筛选条件不足',
                        type: 'warning'
                    });
                    this.loading = false
                    return;
                }

                this.link = './../worklist/export?start_date=' + start_date + '&end_date=' + end_date + '&time_point_start=' + this.timeInterval + '&time_point_end=' + this.timeInterval1 + '&service_type=' + this.value + '&city=' + this.city + '&is_mark=' + this.is_mark;
                window.open(this.link, "_blank");
                this.loading = false
                // this.checkUser = ''
            },
            // openUrl(url) {
            //     window.open(url, "_blank");
            // },


        },
        mounted() {
            this.loading = true
            this.defaultDate(),
                fetch("./../worklist/area", {
                    method: "post"
                }).then(result => {
                    // console.log(result);
                    return result.json();
                }).then(res => {
                    if (res.code == 1) {
                        this.options1 = res.data;
                        this.show_export = true;
                        setTimeout(() => {
                            this.loading = false
                        }, 100);
                    } else {
                        this.$message({
                            message: '暂无数据',
                            type: 'warning'
                        });
                    }
                })
        }
    })


</script>
