<?php
$this->pageTitle = Yii::app()->name . ' - Riskrank';
?>

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
                    <span class="demonstration">城市选择：</span>
                    <el-select style="width:120px" v-model="City" placeholder="请选择" @change="changeCity">
                        <el-option
                                v-for="(item, index) in CityList"
                                :key="index"
                                :label="item.Text"
                                :value="item.City">
                        </el-option>
                    </el-select>
                </div>
                <div>
                    <span class="demonstration">人员选择：</span>
                    <el-select style="width:120px" clearable v-model="Staff" placeholder="请选择" @change="changeUser">
                        <el-option
                                v-for="(item, index)  in StaffList"
                                :key="index"
                                :label="item.StaffName"
                                :value="item.StaffID">
                        </el-option>
                    </el-select>
                </div>
                <div>
                    <span class="demonstration">日期：</span>
                    <el-date-picker
                            v-model="origin_time"
                            type="daterange"
                            align="left"
                            unlink-panels
                            range-separator="至"
                            start-placeholder="开始日期"
                            end-placeholder="结束日期"
                            :picker-options="pickerOptions">
                    </el-date-picker>
                </div>
                <div style="display: block;padding-top: 10px">
                    <el-button style="margin-left: 20px" type="primary" @click="exportAreaData">导出</el-button>
                </div>
            </div>
        </div>
    </div>


</section>

<?php $this->endWidget(); ?>

<script src="<?php echo Yii::app()->baseUrl.'/js/vue.js' ?>"></script>
<script src="<?php echo Yii::app()->baseUrl.'/js/element.js' ?>"></script>


<!-- 引入样式 -->
<link rel="stylesheet" href="<?php echo Yii::app()->baseUrl.'/css/element-ui.css' ?>">
<!-- 引入组件库 -->

<script>
    new Vue({
        el: '#app',
        data() {
            return {
                timeInterval: '00:10',
                timeInterval1: '00:00',
                timeIntervalStart: '00:00',
                timeIntervalEnd: '00:00',
                loading: true,
                origin_time: [],
                CityList: [],
                StaffList: [],
                mark_exception: true,
                is_mark: 1,
                checkUser: '',
                city: '',
                City: [],
                Staff: [],
                switch_value: true,
                click_staff:'',
                link: '',
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
        //监听数据
        watch: {
            StaffList: function (newValue) {
                this.Staff = [];
                this.checkUser = '';
                this.StaffList = newValue;
            },
            timeIntervalStart: function (newValue) {
                this.timeIntervalEnd = newValue
            }
        },
        computed: {},
        mounted() {
            this.loading = true
            this.defaultDate(),
                fetch("./../worklist/area", {
                    method: "post"
                }).then(result => {
                    return result.json();
                }).then(res => {
                    if (res.code == 1) {
                        this.CityList = res.data;
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
        },
        methods: {
            //默认时间
            defaultDate() {
                let date = new Date()
                // 通过时间戳计算
                let defalutStartTime = date.getTime() - 1000 * 24 * 3600 * 7 // 转化为时间戳
                let defalutEndTime = date.getTime()
                let startDateNs = new Date(defalutStartTime)
                let endDateNs = new Date(defalutEndTime)
                // 月，日 不够10补0
                defalutStartTime = startDateNs.getFullYear() + '-' + ((startDateNs.getMonth() + 1) >= 10 ? (startDateNs.getMonth() + 1) : '0' + (startDateNs.getMonth() + 1)) + '-' + (startDateNs.getDate() >= 10 ? startDateNs.getDate() : '0' + startDateNs.getDate())
                defalutEndTime = endDateNs.getFullYear() + '-' + ((endDateNs.getMonth() + 1) >= 10 ? (endDateNs.getMonth() + 1) : '0' + (endDateNs.getMonth() + 1)) + '-' + (endDateNs.getDate() >= 10 ? endDateNs.getDate() : '0' + endDateNs.getDate())

                this.origin_time = [defalutStartTime, defalutEndTime]
            },
            // 格式化时间
            formatDate(d,type = 0) {
                let date = new Date(d);
                let YY = date.getFullYear() + '-';
                let MM =
                    (date.getMonth() + 1 < 10
                        ? '0' + (date.getMonth() + 1)
                        : date.getMonth() + 1) + '-';
                let DD = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
                let hh =
                    (date.getHours() < 10 ? '0' + date.getHours() : date.getHours()) + ':';
                let mm =
                    (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes()) +
                    ':';
                let ss =
                    date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds();
                let result;
                if(type == 1){
                    result = YY + MM + DD;
                }else{
                    result = YY + MM + DD + ' ' + hh + mm + ss;
                }
                return result;
            },
            //城市选择
            changeCity(val) {
                this.city = val

                //获取对应城市下技术人员
                fetch("./../worklist/staff?city=" + val, {
                    method: "get",
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(result => {
                    return result.json();
                }).then(res => {
                    if (res.code == 1) {
                        this.StaffList = res.data;
                    } else {
                        this.$message({
                            message: '暂无数据',
                            type: 'warning'
                        });
                        this.Staff = []
                        this.StaffList = [];
                        setTimeout(() => {
                            this.loading = false
                        }, 100);
                    }

                })
            },
            //人员选择
            changeUser(val) {
                if (this.StaffList.length>0 && val !== '') {
                    this.checkUser = val
                } else {
                    this.checkUser = ''
                }
            },
            //导出城市数据
            exportAreaData() {
                /* 驗證數據 */
                let rule_flag = false
                let msg = '筛选条件不足'
                if(this.city === '') {
                    msg = '請選擇城市'
                }else if(this.checkUser === ''){
                    msg = '請選擇人員'
                }else if (!this.origin_time){
                    msg = '至少选择一个时间段作为筛选条件';
                }else{
                    rule_flag = true
                }
                if(!rule_flag){
                    this.$message({message: msg, type: 'warning'});
                    this.loading = false
                    return;
                }

                /* 构造数据 */
                let start_date = this.formatDate(this.origin_time[0],0);
                let end_date = this.formatDate(this.origin_time[1],0);
                this.is_mark = (this.switch_value===true ? 1: 0)

                this.loading = true

                this.link = './../worklist/evaluationExport?start_date=' + start_date +
                    '&end_date=' + end_date +
                    '&city=' + this.city +
                    '&is_mark=' + this.is_mark+
                    '&staff_id=' + this.checkUser;
                window.open(this.link, "_blank");

                this.loading = false
            },
        },
    })


</script>