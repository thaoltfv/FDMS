<template>
<ValidationObserver tag="form" ref="tab_other_property">
  <div class="row mt-3 content_detail_asset">
      <div v-if="editAsset || form.other_assets && form.other_assets.length > 0" class="col-12">
        <div class="main-wrapper">
            <div class="responsive-table">
              <div class="row container_header">
                <div class="col class_header class_content">Tên tài sản</div>
                <div class="col-2 class_header class_content">Số lượng</div>
                <div class="col-1 class_header class_content">ĐVT</div>
                <div class="col-2 class_header class_content">Đơn giá</div>
                <div class="col-3 class_header class_content">Thành tiền</div>
                <div v-if="editAsset" class="col-2 class_header class_content">Thao tác</div>
              </div>
              <div v-for="(other_property, index) in form.other_assets" :key="index"  class="row">
                <div class="col class_content">
                  <InputText
										:disabledInput="!editAsset"
                    v-model="other_property.name"
                    :vid="'namePropertyOther' + index"
                    nonLabel="Tên tài sản"
                    rules="required"
                  />
                </div>
                <div class="col-2 class_content">
                  <InputNumberNoneFormat
										:disabled="!editAsset"
                    v-model="other_property.total"
                    :vid="'total_amount_other'+ index"
                    class="label-none"
                    label="Số lượng"
                    :max="99999999999999"
                    :min="0"
                    rules="required"
                    @change="changeQuantity($event, index)"
                  />
                </div>
                <div class="col-1 class_content">
                  <InputText
										:disabledInput="!editAsset"
                    v-model="other_property.dvt"
                    :vid="'typePropertyOther' + index"
                    nonLabel="Đvt"
                    :max-length="200"
                    rules="required"
                  />
                </div>
                <div class="col-2 class_content">
                  <InputCurrency
										:disabled="!editAsset"
                    v-model="other_property.unit_price"
                    vid="unit_price_m2"
                    class="label-none"
                    label="Đơn giá"
                    rules="required"
                    @change="changeUnitPrice($event,index)"
                  />
                </div>
                <div class="col-3 class_content">
                  <InputCurrency
                    :key="key_render"
                    v-model="other_property.total_price"
                    class="label-none"
                    nonLabel="thành tiền"
                    :disabled="true"
                  />
                </div>
                <div v-if="editAsset" class="d-flex justify-content-center class_content col-2">
                  <button v-if="editAsset" class="btn-delete" type="button" @click="handleEditOtherProperty(index, other_property)"><img src="@/assets/icons/ic_edit_2.svg" alt="add"/></button>
                  <button v-if="editAsset" class="btn-delete" type="button" @click="handleDeleteOtherProperty(index)"><img src="@/assets/icons/ic_delete_2.svg" alt="delete_land"></button>
                </div>
              </div>
            </div>
            <div class="d-flex">
              <div class="w-100 d-flex justify-content-end">
                <button v-if="editAsset" class="btn text-warning btn-ghost btn-add" type="button" @click="addOtherAsset">
                  <img src="@/assets/icons/ic_add-white.svg" alt="add">
                  + Thêm
                </button>
              </div>
            </div>
        </div>
      </div>
      <div v-if="!editAsset && form.other_assets && form.other_assets.length === 0" class="col-12">
        <div class="infor-box">
          <svg style="margin-right: 1rem" width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 6.64429C12 9.95873 9.31348 12.6443 6 12.6443C2.68652 12.6443 0 9.95873 0 6.64429C0 3.33178 2.68652 0.644287 6 0.644287C9.31348 0.644287 12 3.33178 12 6.64429ZM6 7.85396C5.38536 7.85396 4.8871 8.35223 4.8871 8.96687C4.8871 9.5815 5.38536 10.0798 6 10.0798C6.61464 10.0798 7.1129 9.5815 7.1129 8.96687C7.1129 8.35223 6.61464 7.85396 6 7.85396ZM4.9434 3.85366L5.12286 7.14398C5.13126 7.29795 5.25856 7.41848 5.41275 7.41848H6.58725C6.74144 7.41848 6.86874 7.29795 6.87714 7.14398L7.0566 3.85366C7.06568 3.68735 6.93327 3.54751 6.76672 3.54751H5.23326C5.06671 3.54751 4.93432 3.68735 4.9434 3.85366Z" fill="#007EC6"/>
          </svg>
          Tài sản thẩm định không có thông tin tài sản khác
        </div>
      </div>
      <div class="btn-footer d-md-flex d-block justify-content-end align-items-center w-100">
        <div class="d-md-flex d-block">
          <button  @click="onCancel" class="btn btn-white text-nowrap" >
            <img src="@/assets/icons/ic_cancel.svg" style="margin-right: 12px" alt="save" />Thoát
          </button>
          <button v-if="editAsset" :class="{ 'btn_loading disabled': isSubmit }" class="btn btn-white btn-orange text-nowrap" @click.prevent="handleSaveOtherAsset">
            <img src="@/assets/icons/ic_save.svg" style="margin-right: 12px" alt="save"/>Lưu
          </button>
        </div>
      </div>
      <ModalEditDescriptionStep7
        :key="key_render_description"
        v-if="showDescription"
        :description="description"
        @cancel="showDescription = false"
        @action="handleChangeDescription"
      />
    </div>
</ValidationObserver>
</template>

<script>
import InputNumberNoneFormat from '@/components/Form/InputNumberNoneFormat'
import InputCurrency from '@/components/Form/InputCurrency'
import InputPercent from '@/components/Form/InputPercent'
import InputText from '@/components/Form/InputText'
import CertificateAsset from '@/models/CertificateAsset'
import ModalEditDescriptionStep7 from './ModalEditDescriptionStep7'

import Vue from 'vue'
import Icon from 'buefy'
Vue.use(Icon)
export default {
	name: 'tab_other_property',
	props: ['data', 'idData', 'edit', 'status', 'checkRole', 'editAsset'],
	components: {
		InputText,
		InputCurrency,
		InputPercent,
		InputNumberNoneFormat,
		ModalEditDescriptionStep7
	},
	computed: {
		optionsJuridicals () {
			return {
				data: this.juridicals,
				id: 'id',
				key: 'content'
			}
		}
	},
	data () {
		return {
			form: JSON.parse(JSON.stringify(this.data)),
			key_render: 6152,
			key_render_description: 3210,
			showDescription: false,
			description: '',
			indexEdit: '',
			isSubmit: false
		}
	},
	mounted () {

	},
	beforeUpdate () {
	},
	methods: {
		onCancel () {
			return this.$router.push({name: 'certification_asset.index'})
		},
		addOtherAsset () {
			this.form.other_assets.push({
				name: '',
				description: '',
				total: '',
				dvt: '',
				unit_price: '',
				total_price: ''
			})
		},
		handleEditOtherProperty (index, item) {
			this.showDescription = true
			this.description = item.description
			this.indexEdit = index
			this.key_render_description += 1
		},
		handleChangeDescription (description) {
			this.showDescription = false
			this.form.other_assets[this.indexEdit].description = description
		},
		handleDeleteOtherProperty (index) {
			this.form.other_assets.splice(index, 1)
		},
		changeUnitPrice (e, index) {
			if (e) {
				this.form.other_assets[index].unit_price = e
				this.form.other_assets[index].total_price = e * (this.form.other_assets[index].total ? this.form.other_assets[index].total : 0)
			} else {
				this.form.other_assets[index].unit_price = ''
				this.form.other_assets[index].total_price = ''
			}
			this.key_render += 1
		},
		changeQuantity (e, index) {
			if (e) {
				this.form.other_assets[index].total = e
				this.form.other_assets[index].total_price = e * (this.form.other_assets[index].unit_price ? this.form.other_assets[index].unit_price : 0)
			} else {
				this.form.other_assets[index].total = ''
			}
			this.key_render += 1
		},

		async handleSaveOtherAsset () {
			const valid = await this.$refs.tab_other_property.validate()
			if (valid) {
				await this.saveOtherAsset()
			}
		},
		async saveOtherAsset () {
			this.isSubmit = true
			const data = {
				other_assets: this.form.other_assets
			}
			const res = await CertificateAsset.submitOtherAssetStep7(data, this.idData)
			if (res.data) {
				this.$toast.open({
					message: 'Lưu tài sản khác thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})

				await this.$emit('updateTotalOtherAsset', res.data)
			} else if (res.error) {
				await this.$toast.open({
					message: `${res.error.message}`,
					type: 'error',
					position: 'top-right'
				})
			} else {
				await this.$toast.open({
					message: 'Lưu thất bại',
					type: 'error',
					position: 'top-right'
				})
			}
			this.isSubmit = false
		}
	}

}
</script>
<style scoped lang="scss">
.btn_loading {
    position: relative;
    color: white !important;
    text-shadow: none !important;
    pointer-events: none;
  }
  .btn_loading:after {
    content: '';
    display: inline-block;
    vertical-align: text-bottom;
    border: 1px solid wheat;
    border-right-color: transparent;
    border-radius: 50%;
    color: #ffffff;
    position: absolute;
    width: 1rem;
    height: 1rem;
    left: calc(50% - .5rem);
    top: calc(50% - .5rem);
    -webkit-animation: spinner-border .75s linear infinite;
    animation: spinner-border .75s linear infinite;
  }
.card {
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  background: #FFFFFF;
  margin-bottom: 1rem;

  &-footer {
    padding: 15px 24px;
  }

  &-title {
    background: #F3F2F7;
    padding: 16px 20px;
    margin-bottom: 0;

    &__img {
      padding: 8px 20px;
    }

    @media (max-width: 768px) {
      padding: 12px;
    }

    .title {
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 0;
    }
  }

  &-body {

    @media (max-width: 787px) {
      padding: 15px;
    }
  }

  &-sub_header_title {
    padding: 15px 24px;
  }

  &-info {
    .title {
      font-size: 1.125rem;
      font-weight: 700;
      margin-top: 28px;

      &-highlight {
        background: rgba(252, 194, 114, 0.53);
        text-align: center;
        padding: 10px 0;
        border-radius: 2px;
      }
    }
  }

  &-land {
    position: relative;
    padding: 0;
  }
}

.form-group-container {
  margin-top: 10px;
}

.color-black {
  color: #333333;
}

.btn-delete {
  cursor: pointer;
  display: flex;
  align-items: center;
  background: #FFFFFF;
  border: none;

  // margin: auto;
  // width: 36px;
  // height: 36px;

  img {
    width: 100%;
    height: auto;
    min-width: 0.75rem;
  }
}

.btn {
  &-orange {
    background: #FAA831;
    text-align: center;
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
    height: 35px;
    width: 100%;
    color: #fff;
    box-sizing: border-box;

    &:hover {
      border-color: #dc8300;
    }
  }
}

.img-dropdown {
  cursor: pointer;
  width: 18px;

  &__hide {
    transform: rotate(90deg);
    transition: .3s;
  }
}

.img-locate {
  cursor: pointer;
  position: absolute;
  right: 14px;
  top: 2.1rem;
  background: #FFFFFF;
  height: 2.1rem;
  width: 32px;
  display: grid;
  place-items: center;

  img {
    height: 60%;
  }
}

.text-error {
  color: #cd201f;
  font-size: 12px;
}

.select-group {
  background-color: #F6F7FB;
  border: 1px solid #E8E8E8;
  border-radius: 3px;
  padding: 16px 22px;

  .select-title {
    color: #FAA831;
    font-weight: 700;
    white-space: nowrap;
  }
}
  .img_add {
    width: 100%;
    height: 100% !important;
    cursor: pointer;
  }
  .container_input {
    border-radius: 10px;
    border: 2px solid #FAA831;
    width: 100%;
    height: 100%;
    position: relative;
  }
  .input_file_4 {
    left: 0;
    opacity: 0;
    height: 100%;
    width: 100%;
    cursor: pointer;
    position: absolute;
  }
  .sub_header_title {
    background-color: #F6F7FB;
    border: 1px solid #E8E8E8;
    border-radius: 3px;
    padding: 0.5rem 2rem;
    position: relative;
    color: #00507C;
    font-weight: 700;

    .label {
      margin-right: 15px;
    }
    label {
      margin: 0;
    }
    &::before {
      content: '';
      position: absolute;
      height: calc(100% - 16px);
      width: 3px;
      background-color: #99D161;
      border-radius: 3px;
      top: 50%;
      left: 0;
      transform: translateY(-50%);
    }
  }
  .sub_header_title-rows {
    padding-top: 10px;
  }
  .footer-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #00507C;
  }
  /deep/ {
    .form-group-container.disabled {
      background-color: rgba(222, 230, 238, 0.3);
      .ant-input {
        background-color: rgba(222, 230, 238, 0.3) !important;
      }
    }
  }
  .container_land {
    padding: unset;
    width: 100%;
    display: flex;
  }
  .name_law {
    @media (max-width: 1600px) {
      width: 250px;
    }
    @media (min-width: 1600px) {
      width: 280px;
    }
    @media (min-width: 1900px) {
      width: 300px;
    }

    white-space: nowrap;
    -webkit-line-clamp: 2 !important;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0;
    text-transform: none;

    &:first-letter {
      text-transform: none;
    }
  }
  .agency_law {
    width: 150px;
    white-space: nowrap;
    -webkit-line-clamp: 2 !important;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 0;
    text-transform: none;

    &:first-letter {
      text-transform: none;
    }
  }
.table_legal {
    width: 100%;
    font-weight: 500;
    color: #000000;
    text-align: center;
    thead{
      th{
        padding: 12px 0;
        font-weight: 700;
        background-color: #f29003;
        color: #FFFFFF;
      }
    }
    tbody{
      td{
        border: 1px solid #E5E5E5;
        &:first-child{
          width: 7%;
        }
        &:nth-child(2) {
          max-width: 300px
        }
        &:last-child{
          width: 10%;
        }
        box-sizing: border-box;
        padding: 10px 14px;
      }
    }
  }
  .main-wrapper {
    width: 100%;
    overflow-x: auto;
    box-sizing: border-box;
  }

  .responsive-table {
    display: inline-block;
    min-width: 100%;
    box-sizing: border-box;
  }

  .responsive-table > table {
    width: 100%;
    border-collapse: collapse;
  }
  .class_content {
    padding: 0.75rem;
    border: 1px solid #E8E8E8;
  }
  .class_content_header {
    padding: 1rem 1.3rem;
    border: 1px solid #E8E8E8;
  }
  .class_header {
    background: #DEE6EE;
    color: #3D4D65;
    font-weight: 700;
    text-align: center;
  }
  // .container_header {
  //   border: 1px solid #E8E8E8;
  //   border-radius: 3px 3px 0px 0px;
  // }
  .row {
  margin-right: unset !important;
  margin-left: unset !important;
}
.infor-box {
    padding: 1rem;
    border-radius: 12px 15px;
    background-color: #EEF9FF;
    border: 1px solid #007EC6;
    color: #446B92;
    @media (max-height: 660px) {
      font-size: 12px;
    }
    @media (max-height: 970px) and (min-height: 660px) {

    }
  }
</style>
