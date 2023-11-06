<template>
<div>
    <ValidationObserver tag="form"
                        ref="formLegal"
                        @submit.prevent="validateLegal">
    <div class="modal-detail d-flex justify-content-center align-items-center" >
        <div class="card">
        <div class="container-title">
            <div class="d-lg-flex d-block shadow-bottom">
            <h2 class="title">Thông tin về pháp lý tài sản</h2>
            </div>
        </div>
        <div class="contain-detail">
                    <div class="row">
                        <div class="col-12 col-xl-6">
                            <div class="row flex-column h-100">
                                <div class="col">
                                    <InputCategory
                                        v-model="form.appraise_law_id"
                                        vid="appraise_law_id"
                                        label="Loại pháp lý"
                                        rules="required"
                                        :options="optionsJuridicals"
                                        @change="handleChangeTypeLegal"
                                    />
                                </div>
                                <div v-if="form.appraise_law_id === 0" class="col">
                                    <InputText
                                            v-model="form.description"
                                            vid="description"
                                            class="form-group-container"
                                            label="Tên pháp lý"
                                            rules="required"
                                        />
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <InputTextarea
                                            v-model="form.date"
                                            vid="date"
                                            class="form-group-container col-6"
                                            label="Số pháp lý"
                                            rules="required"
                                            :autosize="true"
                                        />
                                        <InputDatePicker
                                            v-model="form.law_date"
                                            vid="law_date"
                                            label="Ngày pháp lý"
                                            placeholder="Ngày/tháng/năm"
                                            class="form-group-container col-6"
                                            formatDate='DD/MM/YYYY'
                                            @change="changeLegalDate"
                                        />
                                    </div>
                                </div>
                                <div class="col input-contain" v-if="form.appraise_law_id !== 0">
                                    <InputText
                                        v-model="form.duration"
                                        vid="duration"
                                        label="Thời hạn sử dụng"
                                        class="form-group-container"
                                    />
                                </div>
                                <div class="col" ref="landDetails" v-if="form.appraise_law_id !== 0">
                                    <div class="row" v-for="(itemLand, index) in form.land_details" :key="index">
                                        <div class="col-12 col-lg-6 item_land input-contain">
                                            <InputCategoryCustom
                                                v-model="itemLand.land_type_purpose_id"
                                                vid="land_type_purpose_id"
                                                class="form-group-container"
                                                label="Mục đích sử dụng"
                                                :options="optionsTypePurposes"
                                                @change="changeLandtypepurpose($event, index)"
                                            />
                                        </div>
                                        <div class="col-12 col item_land input-contain" :class="[form.land_details.length > 1 ? 'col-lg-5' : 'col-lg-6']">
                                            <InputAreaCustom
                                                v-model="itemLand.total_area"
                                                vid="total_area"
                                                label="Diện tích"
                                                class="form-group-container"
                                                @change="changeTotal_area($event, index)"
                                            />
                                        </div>
                                        <div class="col-12 col-lg-6 item_land input-contain">
                                            <InputText
                                                v-model="itemLand.doc_no"
                                                vid="doc_num"
                                                label="Số tờ"
                                                class="form-group-container"
                                                @change="changeDocNo($event, index)"
                                            />
                                            
                                        </div>
                                        <div class="col-12 col item_land input-contain" :class="[form.land_details.length > 1 ? 'col-lg-5' : 'col-lg-6']">
                                            <InputText
                                                v-model="itemLand.land_no"
                                                vid="plot_num"
                                                label="Số thửa"
                                                class="form-group-container"
                                                @change="changeLandNo($event, index)"
                                            />
                                        </div>
                                        <div v-if="form.land_details.length > 1" class="button_delete_land col-12 col-lg-1 d-flex align-items-end p-0">
                                            <button class="btn-delete" type="button" @click="handleDeleteLand(index)">
                                                <img alt="delete_land" src="@/assets/icons/ic_delete_2.svg">
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="d-flex justify-content-end w-100 pr-0">
                                            <button class="btn text-warning btn-ghost btn-add pr-0" type="button" @click="handleAddLandDoc">
                                                <img alt="add" src="@/assets/icons/ic_add-white.svg" class="mr-0">
                                                + Thêm
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-xl-6">
                            <div class="row flex-column h-100">
                                <div class="col input-contain">
                                    <InputText
                                        v-model="form.certifying_agency"
                                        vid="certifying_agency"
                                        label="Cơ quan các cấp xác nhận"
                                        rules="required"
                                    />
                                </div>
                                <div class="col" v-if="form.appraise_law_id !== 0">
                                    <InputText
                                        v-model="form.legal_name_holder"
                                        class="form-group-container"
                                        vid="name_building"
                                        label="Người đứng tên pháp lý"
                                        @change="changeLegal"
                                        rules="required"
                                    />
                                </div>
                                <div class="col input-contain">
                                    <InputTextarea
                                        v-model="form.origin_of_use"
                                        vid="origin_of_use"
                                        label="Nguồn gốc sử dụng"
                                        class="form-group-container"
                                        :autosize="true"
                                    />
                                </div>
                                <div class="col input-contain">
                                    <InputTextarea
                                        v-model="form.content"
                                        vid="content"
                                        label="Nội dung"
                                        rules="required"
                                        class="form-group-container"
                                        :rows="contentRows"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
        </div>
        <div class="container-title container-title__footer">
            <div class="d-lg-flex d-block justify-content-end shadow-bottom">
            <button class="btn btn-white" type="button" @click="handleCancel"><img src="@/assets/icons/ic_cancel.svg" style="margin-right: 5px" alt="cancel"> Trở lại</button>
            <button class="btn btn-white btn-orange text-nowrap" type="button" @click.prevent="validateLegal"><img src="@/assets/icons/ic_save.svg" style="margin-right: 5px" alt="save"> Lưu</button>
            </div>
        </div>
        </div>
    </div>
    </ValidationObserver>
</div>
</template>

<script>
import InputNumberNoneFormat from '@/components/Form/InputNumberNoneFormat'
import InputCategory from '@/components/Form/InputCategory'
import InputText from '@/components/Form/InputText'
import InputTextarea from '@/components/Form/InputTextarea'
import InputDatePicker from '@/components/Form/InputDatePicker'
import InputAreaCustom from '@/components/Form/InputAreaCustom'
import InputCategoryCustom from '@/components/Form/InputCategoryCustom'
import WareHouse from '@/models/WareHouse'
// import moment from 'moment'
export default {
    name: 'ModalBuildingDetail',
    props: ['data', 'juridicals', 'provinceName', 'full_address'],
    components: {
        InputCategory,
        InputText,
        InputTextarea,
        InputNumberNoneFormat,
        InputDatePicker,
        InputAreaCustom,
        InputCategoryCustom
    },
    data () {
        return {
            form: this.data ? JSON.parse(JSON.stringify(this.data)) : {},
            contentRows: 3,
            type_purposes: []
        }
    },

    computed: {
        optionsJuridicals () {
            return {
                data: this.juridicals,
                id: 'id',
                key: 'content'
            }
        },
        optionsTypePurposes () {
            return {
                data: this.type_purposes,
                id: 'id',
                key: 'description'

            }
        }

    },
    mounted () {
        this.setContentRows()
    },
    async beforeMount () {
		this.getDictionaryLand()
	},
    methods: {
		async getDictionaryLand () {
			const resp = await WareHouse.getDictionariesLand()
			this.type_purposes = [...resp.data]
			this.type_purposes.forEach(item => {
				item.description = this.formatSentenceCase(item.description)
			})
		},
        formatSentenceCase (phrase) {
			let text = phrase.toLowerCase()
			return text.charAt(0).toUpperCase() + text.slice(1)
		},
		async getAppraiseLaws () {
			await Certificate.getAppraiseLaws().then((resp) => {
				if (resp.data && resp.data.phap_ly) {
					this.juridicals = resp.data.phap_ly
					this.juridicals.push({
						content: 'Văn bản pháp lý khác',
						created_at: new Date(),
						date: '',
						deleted_at: null,
						document_type: '',
						id: 0,
						is_defaults: false,
						provinces: 'Tất cả',
						type: 'PHAP_LY'
					})
				} else {
					this.juridicals = []
				}
			})
		},
        changeLegalDate (event) {
            if (event) { this.form.law_date = event }
        },
        handleAddLandDoc () {
            this.form.land_details.push({
                doc_no: '',
                land_no: ''
            })
            this.setContentRows()
        },
        handleAddLandMainArea () {
            this.form.land_details.push({
                land_type_purpose_id: '',
                total_area: ''
            })
            this.setContentRows()
        },
        changeLegal () {
            this.getContent()
        },
        changeDocNo (event, index) {
            if (event) {
                this.form.land_details[index].doc_no = event
            } else {
                this.form.land_details[index].doc_no = ''
            }
            this.getContent()
        },
        changeLandNo (event, index) {
            if (event) {
                this.form.land_details[index].land_no = event
            } else {
                this.form.land_details[index].land_no = ''
            }
            this.getContent()
        },
        changeLandtypepurpose (event, index) {
            if (event) {
                this.form.land_details[index].land_type_purpose_id = event
            } else {
                this.form.land_details[index].land_type_purpose_id = ''
            }
            this.getContent()
        },
        changeTotal_area (event, index) {
            if (event) {
                this.form.land_details[index].total_area = event
            } else {
                this.form.land_details[index].total_area = ''
            }
            this.getContent()
        },
        async getContent () {
            let land_description = ''
            console.log('data', this.form)
            const map = new Map()
            if (this.form.land_details.length > 0) {
                await this.form.land_details.forEach(item => {
                    let land_no_description = ''
                    let land_description_item = ''
                    if (!map.has(item.doc_no)) {
                        map.set(item.doc_no, true)
                        let filterArray = this.form.land_details.filter(itemFilter => item.doc_no === itemFilter.doc_no)
                        land_description_item = ''
                        land_no_description = ''
                        let land_no_number = null
                        if (filterArray.length > 0) {
                            filterArray.forEach(landItem => {
                                land_no_number = landItem.land_no
                                if (!land_no_description) {
                                    land_no_description = `${land_no_description} ` + `${land_no_number === 0 ? 0 : land_no_number || ''}`
                                } else land_no_description = `${land_no_description}, ` + `${land_no_number}`
                            })
                        }
                        land_description_item = 'thửa đất số' + `${land_no_description || ''}` + ' tờ bản đồ số ' + `${item.doc_no || item.doc_no === 0 ? item.doc_no : ''}`
                        if (!land_description) {
                            land_description = `${land_description}` + `${land_description_item}`
                        } else land_description = `${land_description}, ` + `${land_description_item}`
                    }
                })
                this.setContentRows()
            }
            this.form.content = await 'Chứng nhận ' + `${this.form.legal_name_holder ? this.form.legal_name_holder + ' ' : ''}` + 'được quyền sử dụng đất thuộc ' + `${land_description} ` + `${this.full_address ? this.full_address : ''}.`
            land_description = ''
        },
        getProvince () {
            this.form.certifying_agency = `Sở Tài nguyên và Môi trường ${this.provinceName && this.provinceName.toLowerCase().includes('thành phố') ? this.provinceName : this.provinceName ? 'Tỉnh ' + this.provinceName : ''}`
        },
        setContentRows () {
            if (this.form.appraise_law_id === 0) {
                this.contentRows = 9
            } else this.contentRows = this.form.land_details.length * 3
        },
        handleDeleteLand (index) {
            this.form.land_details.splice(index, 1)
            this.setContentRows()
            this.getContent()
        },
        handleDeleteMainArea (index) {
            this.form.land_details.splice(index, 1)
            this.setContentRows()
            this.getContent()
        },
        handleChangeTypeLegal (event) {
            if (event === 0) {
                this.form.content = ''
                this.form.certifying_agency = ''
                this.form.land_details = [
                    {
                        doc_no: '',
                        land_no: ''
                    }
                ]
            } else {
                this.form.description = ''
                this.getContent()
                this.getProvince()
            }
            this.setContentRows()
        },
        handleCancel (event) {
            this.$emit('cancel', event)
        },
        async validateLegal () {
            const valid = await this.$refs.formLegal.validate()
            console.log('form', this.form)
            if (valid) {
                let getLaw = await this.juridicals.filter(item => item.id === this.form.appraise_law_id)
                this.form.law = getLaw[0]
                let checkDocLand = true
                this.form.land_details.forEach(item => {
                    if ((item.doc_no || item.doc_no === 0) && (item.land_no || item.land_no === 0)) {
                        checkDocLand = false
                    }
                })
                if (checkDocLand && this.form.appraise_law_id !== 0) {
                    return this.$toast.open({
                        message: 'Vui lòng nhập Số tờ , Số thửa',
                        type: 'error',
                        position: 'top-right'
                    })
                }
                await this.handleAction()
            }
        },
        handleAction () {
            this.$emit('action', this.form)
        }
    }

}
</script>

<style lang="scss" scoped>
.btn-delete {
  cursor: pointer;
  display: flex;
  align-items: center;
  background: #FFFFFF;
  border: none;
    margin-bottom: 0.6rem;
  img {
    width: 100%;
    height: auto;
    min-width: 0.75rem;
  }
}
.title{
font-size: 1.125rem;
font-weight: 700;
margin-bottom: 25px;
color: #000000;
}
.modal-detail {
position: fixed;
z-index: 1031;
left: 0;
top: 0;
width: 100%;
height: 100%;
background: rgba(0,0,0,.6);
.card {
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    max-width: 1300px;
    width: 100%;
    max-height: 90vh;
    margin-bottom: 0;
    padding: 35px 95px;
    @media (max-width: 787px) {
    padding: 20px 10px;
    }
    &-header {
    border-bottom: 1px solid #DDDDDD;
    h3 {
        color: #333333;
    }
    img {
        cursor: pointer;
    }
    }
    &-body {
    text-align: center;
    p {
        color: #333333;
        margin-bottom: 40px;
    }

    .btn__group {
        .btn {
        max-width: 150px;
        width: 100%;
        margin: 0 10px;
        }
    }
    }
}
}
.card{
.contain-detail{
    overflow-y: auto;
    overflow-x: hidden;
    margin-top: 20px;
    &::-webkit-scrollbar{
    width: 2px;
    }
}
&-title{
    background: #F3F2F7;
    padding: 16px 20px;
    margin-bottom: 0;
    .title{
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0;
    }
}
&-table{
    border-radius: 5px;
    background: #FFFFFF;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    width: 99%;
    margin: 50px auto 50px;
}
&-body{
    padding: 35px 30px 40px;
}
&-info{
    .title{
    font-size: 1.125rem;
    font-weight: 700;
    margin-top: 28px;
    }
}
&-land{
    position: relative;
    padding: 0;
}
}
.img{
margin-right: 13px;
}
.card{
border-radius: 5px;
box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);;
background: #FFFFFF;
margin-bottom: 75px;
&-title{
    background: #F3F2F7;
    padding: 16px 20px;
    margin-bottom: 0;
    .title{
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0;
    }
}
&-body{
    padding: 35px 30px 40px;
}
&-info{
    .title{
    font-size: 1.125rem;
    font-weight: 700;
    margin-top: 28px;
    }
}
&-land{
    position: relative;
    padding: 0;
}
}
.card__order{
max-width: 50%;
margin-bottom: 1.25rem;
@media (max-width: 767px) {
    max-width: 100%;
}
}
.btn{
&-white{
    max-height: none;

    line-height: 19.07px;
    margin-right: 15px;
    &:last-child{
    margin-right: 0;
    }
}
&-contain{
    margin-bottom: 55px;
}
}
.d-grid{
display: grid;
grid-template-columns: 1fr 1fr;
grid-column-gap: 8.9%;
&:first-child {
    margin-top: 0;
}
&__checkbox{
    grid-template-columns: 1fr 1fr;
}
@media (max-width: 767px) {
    grid-template-columns: 1fr;
}
}
.content{
&-detail{
}
&-title{
    color: #555555;
    margin-bottom: 5px;

    font-weight: 500;
}
&-name{
    font-size: 1.125rem;
    color: #000000;
    margin-bottom: 15px;
    font-weight: 600;
    &__code{
    color: #FAA831;
    }
}
}
.contain-table{
@media (max-width: 767px) {
    overflow-y: hidden;
    overflow-x: auto;
}
.table-property{
    width: 100%;
}
}
.table-property{
width: 100%;
font-weight: 500;
color: #000000;
text-align: center;
thead{
    th{
    padding: 12px 0;
    font-weight: 500;
    }
}
tbody{
    td{
    border: 1px solid #E5E5E5;
    &:first-child{
        border-left: none;
        width: 180px
    }
    &:last-child{
        border-right: none;
    }
    box-sizing: border-box;
    padding: 14px;
    }
}
}
.img-content{
color: #000000;

font-weight: 600;
span{
    font-weight: 500;
    margin-left: 10px;
}
}
.input-code{
color: #000000;
border-radius: 5px;
width: 180px;
border: 1px solid #000000;
background: #f5f5f5;
height: 35px;
display: flex;
align-items: center;
justify-content: center;
cursor: pointer;
}
.img-dropdown{
cursor: pointer;
width: 18px;
&__hide{
    transform: rotate(90deg);
    transition: .3s;
}
}
//.img-contain {
//  max-width: 200px;
//  max-height: 200px;
//  height: auto;
//  margin-left: 20px;
//  &__table{
//    margin: auto;
//    max-width: 50px;
//    img{
//      cursor: pointer;
//      display: flex;
//      justify-content: center;
//    }
//  }
//  img{
//    object-fit: contain;
//    width: 100%;
//    height: auto;
//    max-width: 200px;
//    max-height: 200px;
//  }
//}
.img-contain {
aspect-ratio: 1/1;
overflow: hidden;
img{
    height: 100%;
    cursor: pointer;
    object-fit: cover;
}
&__table{
    margin: auto;
    max-width: 50px;
    max-height: 50px;
    img{
    object-fit: cover;
    object-position: top;
    cursor: pointer;
    display: flex;
    justify-content: center;
    max-width: 50px;
    max-height: 50px;
    }
}
}
.container-title{
margin: -35px -95px auto;
padding: 35px 95px 0;
box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
.title{
    font-size: 1.2rem;
    @media (max-width: 767px) {
    font-size: 1.125rem;
    }
}
&__footer{
    margin: auto -95px -35px;
    padding: 20px 95px 20px;
    @media (max-width: 767px) {
    .btn-white{
        margin-bottom: 20px;
    }
    }
}
}
.container-img{
padding: .75rem 0;
border: 1px solid #0b0d10;
}
</style>


