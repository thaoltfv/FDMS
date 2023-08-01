<template>
  <div>
    <ValidationObserver tag="form" ref="observer">
      <div
        class="modal-detail d-flex justify-content-center align-items-center"
      >
        <div class="card">
          <div class="container-title">
            <div class="d-lg-flex d-block shadow-bottom">
              <h2 class="title">Chỉnh sửa phụ lục 2</h2>
            </div>
          </div>
          <div class="contain-detail">
            <Tabs :theme="theme" :navAuto="true">
              <TabItem v-for="(item, appIndex) in form.appraises" :key="appIndex" :name="'TSTĐ ' + item.id" v-if="item.tangible_assets && item.tangible_assets.length > 0 ? true : false" >
                <div class="container-table">
                  <table class="table-property">
                    <thead>
                      <tr>
                        <th>
                          Tên tài sản - {{ item.tangible_assets && item.tangible_assets.length > 0 ? item.tangible_assets[0].building_type.description : ""}}
                        </th>
                        <th>Đơn vị tính đ/m<sup>2</sup></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="text-left">Tên đơn vị xây dựng</td>
                        <td>Đơn giá xây dựng</td>
                      </tr>
                      <tr v-for="(company,index) in item.construction_company_custom" :key="company.id">
                        <td class="text-left"> {{ company.name }}</td>
                        <td>
                           <InputNumberFormat
                              v-model="company.unit_price_m2"
                              vid="total_amount"
                              :formatter="valueFormat => `${valueFormat}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')"
                              @change="changeContructionCompany($event, appIndex, index)"
                            />
                          <!-- {{
                            format(company.construction_company.unit_price_m2)
                          }}đ -->
                        </td>
                      </tr>
                      <tr>
                        <td class="text-left">Đơn giá quyết định</td>
                        <td>
                          {{ total_desicion_average && total_desicion_average[item.id] ? `${format(total_desicion_average[item.id])}đ` : `${total_desicion_average[item.id]}` }}
                        </td>
                      </tr>
                      <tr>
                        <td class="text-left">Đơn giá ước tính</td>
                        <td>
                          {{ total_average && total_average.length > 0 ? format(formatCurrent(total_average[appIndex])) : 0 }}đ
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="container-table mt-5" v-if="item.comparison_tangible_factor && item.comparison_tangible_factor.length > 0">
                  <table class="table-property">
                    <thead>
                      <tr>
                        <th>Tên tài sản - {{ item.tangible_assets && item.tangible_assets.length > 0 ? item.tangible_assets[0].building_type.description : "" }}</th>
                        <th>Tiêu chí</th>
                        <th>Giá trị</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td class="text-left" rowspan="2">Móng, khung cột</td>
                        <td>p</td>
                        <td>
                          <InputNumberFormat
                            class="label-none"
                            label="test"
                            vid="test"
                            :min="-10000"
                            @change="changeP1($event, appIndex)"
                            v-model="item.comparison_tangible_factor[0].p1"
                          />
                        </td>
                      </tr>
                      <tr>
                        <td>h</td>
                        <td>
                          <InputNumberFormat
                            class="label-none"
                            label="test"
                            vid="test"
                            :min="-10000"
                            @change="changeH1($event, appIndex)"
                            v-model="item.comparison_tangible_factor[0].h1"
                          />
                        </td>
                      </tr>
                      <tr>
                        <td class="text-left" rowspan="2">Tường</td>
                        <td>p</td>
                        <td>
                          <InputNumberFormat
                            class="label-none"
                            label="test"
                            vid="test"
                            :min="-10000"
                            @change="changeP2($event, appIndex)"
                            v-model="item.comparison_tangible_factor[0].p2"
                          />
                        </td>
                      </tr>
                      <tr>
                        <td>h</td>
                        <td>
                          <InputNumberFormat
                            class="label-none"
                            label="test"
                            vid="test"
                            :min="-10000"
                            @change="changeH2($event, appIndex)"
                            v-model="item.comparison_tangible_factor[0].h2"
                          />
                        </td>
                      </tr>
                      <tr>
                        <td class="text-left" rowspan="2">Nền, sàn</td>
                        <td>p</td>
                        <td >
                            <InputNumberFormat
                              class="label-none"
                              label="test"
                              vid="test"
                              :min="-10000"
                              @change="changeP3($event, appIndex)"
                              v-model="item.comparison_tangible_factor[0].p3"
                            />
                        </td>
                      </tr>
                      <tr>
                        <td>h</td>
                        <td>
                          <InputNumberFormat
                            class="label-none"
                            label="test"
                            vid="test"
                            :min="-10000"
                            @change="changeH3($event, appIndex)"
                            v-model="item.comparison_tangible_factor[0].h3"
                          />
                        </td>
                      </tr>
                      <tr>
                        <td class="text-left" rowspan="2">Kết cấu đỡ mái</td>
                        <td>p</td>
                        <td>
                            <InputNumberFormat
                              class="label-none"
                              label="test"
                              vid="test"
                              :min="-10000"
                              @change="changeD4($event, appIndex)"
                              v-model="item.comparison_tangible_factor[0].d4"
                            />
                        </td>
                      </tr>
                      <tr>
                        <td>h</td>
                        <td>
                          <InputNumberFormat
                            class="label-none"
                            label="test"
                            vid="test"
                            :min="-10000"
                            @change="changeH4($event, appIndex)"
                            v-model="item.comparison_tangible_factor[0].h4"
                          />
                        </td>
                      </tr>
                      <tr>
                        <td class="text-left" rowspan="2">Mái</td>
                        <td>p</td>
                        <td>
                          <InputNumberFormat
                            class="label-none"
                            label="test"
                            vid="test"
                            :min="-10000"
                            @change="changeP5($event, appIndex)"
                            v-model="item.comparison_tangible_factor[0].p5"
                          />
                        </td>
                      </tr>
                      <tr>
                        <td>h</td>
                        <td>
                          <InputNumberFormat
                            class="label-none"
                            label="test"
                            vid="test"
                            :min="-10000"
                            @change="changeH5($event, appIndex)"
                            v-model="item.comparison_tangible_factor[0].h5"
                          />
                        </td>
                      </tr>
                      <tr>
                        <td class="text-left" colspan="2">CLCL (%)</td>
                        <td>
                          {{ total && total.length > 0 ? parseFloat(total).toFixed(0) : 0 }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </TabItem>
            </Tabs>
          </div>
          <div class="container-title container-title__footer">
            <div class="d-flex justify-content-between justify-content-lg-end">
              <button class="btn btn-orange" type="button" @click="saveData">
                <img
                  src="../../../../assets/icons/ic_save.svg"
                  style="margin-right: 12px"
                  alt="save"
                />Lưu
              </button>
              <button
                class="btn btn-white btn-action-modal"
                type="button"
                @click="handleCancel"
              >
                <img
                  src="../../../../assets/icons/ic_cancel.svg"
                  style="margin-right: 12px"
                  alt="save"
                />Trở lại
              </button>
            </div>
          </div>
        </div>
      </div>
    </ValidationObserver>
  </div>
</template>

<script>
import { Tabs, TabItem } from 'vue-material-tabs'
import InputNumberFormat from '@/components/Form/InputNumber'
import InputText from '@/components/Form/InputText'
import Certificate from '@/models/Certificate'
export default {
	name: 'ModalConstructionUnit',
	props: ['formData'],
	data () {
		return {
			appraises: [
				{
					id: 1000
				},
				{
					id: 1001
				},
				{
					id: 1003
				}
			],
			form: this.formData !== undefined && this.formData !== '' ? JSON.parse(JSON.stringify(this.formData)) : {},
			isSubmit: false,
			isLoading: false,
			selectedRowKeys: [],
			theme: {
				navItem: '#000000',
				navActiveItem: '#FAA831',
				slider: '#FAA831',
				arrow: '#000000'
			},
			total: [],
			total_average: [],
			construction_company: [],
			comparison_tangible_factor: [],
			total_desicion_average: {}
		}
	},
	components: {
		InputNumberFormat,
		InputText,
		Tabs,
		TabItem
	},
	computed: {},
	mounted () {
		this.getTotalMounted()
		this.average()
	},
	created () {
		this.getBuildingPrice()
	},
	methods: {
		formatCurrent (value) {
			return Math.round(value / 100000) * 100000
		},
		getBuildingPrice () {
			this.form.appraises.forEach((data, index) => {
				if (data.building_price) {
					this.total_desicion_average[data.id] = data.building_price
				}
			})
		},
		changeContructionCompany (e, appIndex, index) {
			if (e) {
				this.form.appraises[appIndex].construction_company_custom[index].unit_price_m2 = e
				this.average()
			}
		},
		changeP1 (e, appIndex) {
			if (e) {
				this.form.appraises[appIndex].comparison_tangible_factor[0].p1 = e
			} else {
				this.form.appraises[appIndex].comparison_tangible_factor[0].p1 = 0
			}
			this.getTotal(appIndex)
		},
		changeP2 (e, appIndex) {
			if (e) {
				this.form.appraises[appIndex].comparison_tangible_factor[0].p2 = e
			} else {
				this.form.appraises[appIndex].comparison_tangible_factor[0].p2 = 0
			}
			this.getTotal(appIndex)
		},
		changeP3 (e, appIndex) {
			if (e) {
				this.form.appraises[appIndex].comparison_tangible_factor[0].p3 = e
			} else {
				this.form.appraises[appIndex].comparison_tangible_factor[0].p3 = 0
			}
			this.getTotal(appIndex)
		},
		changeD4 (e, appIndex) {
			if (e) {
				this.form.appraises[appIndex].comparison_tangible_factor[0].d4 = e
			} else {
				this.form.appraises[appIndex].comparison_tangible_factor[0].d4 = 0
			}
			this.getTotal(appIndex)
		},
		changeP5 (e, appIndex) {
			if (e) {
				this.form.appraises[appIndex].comparison_tangible_factor[0].p5 = e
			} else {
				this.form.appraises[appIndex].comparison_tangible_factor[0].p5 = 0
			}
			this.getTotal(appIndex)
		},
		changeH1 (e, appIndex) {
			if (e) {
				this.form.appraises[appIndex].comparison_tangible_factor[0].h1 = e
			} else {
				this.form.appraises[appIndex].comparison_tangible_factor[0].h1 = 0
			}
			this.getTotal(appIndex)
		},
		changeH2 (e, appIndex) {
			if (e) {
				this.form.appraises[appIndex].comparison_tangible_factor[0].h2 = e
			} else {
				this.form.appraises[appIndex].comparison_tangible_factor[0].h2 = 0
			}
			this.getTotal(appIndex)
		},
		changeH3 (e, appIndex) {
			if (e) {
				this.form.appraises[appIndex].comparison_tangible_factor[0].h3 = e
			} else {
				this.form.appraises[appIndex].comparison_tangible_factor[0].h3 = 0
			}
			this.getTotal(appIndex)
		},
		changeH4 (e, appIndex) {
			if (e) {
				this.form.appraises[appIndex].comparison_tangible_factor[0].h4 = e
			} else {
				this.form.appraises[appIndex].comparison_tangible_factor[0].h4 = 0
			}
			this.getTotal(appIndex)
		},
		changeH5 (e, appIndex) {
			if (e) {
				this.form.appraises[appIndex].comparison_tangible_factor[0].h5 = e
			} else {
				this.form.appraises[appIndex].comparison_tangible_factor[0].h5 = 0
			}
			this.getTotal(appIndex)
		},
		getTotal (appIndex) {
			const p1 = this.form.appraises[appIndex].comparison_tangible_factor[0].p1
			const p2 = this.form.appraises[appIndex].comparison_tangible_factor[0].p2
			const p3 = this.form.appraises[appIndex].comparison_tangible_factor[0].p3
			const d4 = this.form.appraises[appIndex].comparison_tangible_factor[0].d4
			const p5 = this.form.appraises[appIndex].comparison_tangible_factor[0].p5
			const h1 = this.form.appraises[appIndex].comparison_tangible_factor[0].h1
			const h2 = this.form.appraises[appIndex].comparison_tangible_factor[0].h2
			const h3 = this.form.appraises[appIndex].comparison_tangible_factor[0].h3
			const h4 = this.form.appraises[appIndex].comparison_tangible_factor[0].h4
			const h5 = this.form.appraises[appIndex].comparison_tangible_factor[0].h5
			if (p1 + p2 + p3 + d4 + p5 !== 0) {
				this.total[appIndex] = parseFloat(
					(p1 * h1 + p2 * h2 + p3 * h3 + d4 * h4 + p5 * h5) / (p1 + p2 + p3 + d4 + p5)).toFixed(1)
			} else {
				this.total[appIndex] = 0
			}
		},
		getTotalMounted () {
			this.total = []
			if ((typeof this.form !== 'undefined') && (this.form !== null) && (typeof this.form.appraises !== 'undefined') && (this.form.appraises !== null)) {
				this.form.appraises.forEach((appraise) => {
					if (
						appraise.comparison_tangible_factor &&
          appraise.comparison_tangible_factor.length > 0
					) {
						const p1 = appraise.comparison_tangible_factor[0].p1
						const p2 = appraise.comparison_tangible_factor[0].p2
						const p3 = appraise.comparison_tangible_factor[0].p3
						const d4 = appraise.comparison_tangible_factor[0].d4
						const p5 = appraise.comparison_tangible_factor[0].p5
						const h1 = appraise.comparison_tangible_factor[0].h1
						const h2 = appraise.comparison_tangible_factor[0].h2
						const h3 = appraise.comparison_tangible_factor[0].h3
						const h4 = appraise.comparison_tangible_factor[0].h4
						const h5 = appraise.comparison_tangible_factor[0].h5
						if (p1 + p2 + p3 + d4 + p5 !== 0) {
							this.total.push(
								parseFloat(
									(p1 * h1 + p2 * h2 + p3 * h3 + d4 * h4 + p5 * h5) /
                    (p1 + p2 + p3 + d4 + p5)
								).toFixed(1)
							)
						} else {
							this.total.push(0)
						}
					} else {
						this.total.push(0)
					}
				})
			}
		},
		average () {
			this.total_average = []
			this.form.appraises.forEach((appraise) => {
				let average = 0
				appraise.construction_company_custom.forEach((construction_company) => {
					average =
            average + construction_company.unit_price_m2
				})
				this.total_average.push(
					parseFloat(average / appraise.construction_company_custom.length).toFixed(0)
				)
			})
		},
		getConstructionCompany () {
			this.construction_company = []
			this.form.construction_company_custom.forEach((construction_company) => {
				if (this.construction_company.length < 3) {
					this.construction_company.push(construction_company)
				}
			})
		},
		getData () {
			this.form.appraises.forEach((appraises) => {
				if (appraises.comparison_tangible_factor && appraises.comparison_tangible_factor.length > 0) {
					this.comparison_tangible_factor.push(appraises.comparison_tangible_factor[0])
				}
				if (appraises.construction_company_custom && appraises.construction_company_custom.length > 0) {
					this.construction_company.push(appraises.construction_company_custom)
				}
			})
		},
		async saveData () {
			await this.getData()
			const res = await Certificate.postDataComparisonCert(
				this.comparison_tangible_factor, this.construction_company
			)
			if (res.data) {
				this.$toast.open({
					message: 'Điều chỉnh phụ lục 2 thành công',
					type: 'success',
					position: 'top-right',
					duration: 3000
				})
				this.$emit('cancel')
				this.$emit('action', this.formData.id)
			} else if (res.error) {
				this.$toast.open({
					message: res.error.message,
					type: 'error',
					position: 'top-right',
					duration: 3000
				})
			}
		},
		format (value) {
			let num = (value / 1).toFixed(0).replace(',', '.')
			return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
		},
		handleCancel (event) {
			this.$emit('cancel', event)
		},
		handleAction (event) {
			this.$emit('cancel', event)
			this.$emit('action', this.selectedRowKeys)
		}
	}
}
</script>

<style scoped lang="scss">
.modal-detail {
  position: fixed;
  z-index: 1031;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  @media (max-width: 768px) {
    padding: 20px;
  }
  .card {
    border-radius: 5px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
    max-width: 1400px;
    width: 100%;
    max-height: 90vh;
    margin-bottom: 0;
    padding: 20px 30px;
    @media (max-width: 768px) {
      padding: 20px 10px;
    }
    &-header {
      border-bottom: 1px solid #dddddd;
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
.title-property {
  font-weight: 700;
  font-size: 1.2rem;
  margin-bottom: 18px;
}
.input-contain {
  margin-bottom: 25px;
}
.card-table {
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  background: #ffffff;
  max-width: 99%;
  margin: 50px auto 75px;
}
.card-table tbody tr:last-child td,
.card-table tbody tr:last-child th {
  border-bottom: 1px solid #e5e5e5;
}
.card {
  .contain-detail {
    overflow-y: auto;
    overflow-x: hidden;
    margin-top: 20px;
    margin-bottom: 20px;
    &::-webkit-scrollbar {
      width: 2px;
    }
  }
  &-title {
    background: #f3f2f7;
    padding: 16px 20px;
    margin-bottom: 0;
    &__img {
      padding: 8px 20px;
    }
    .title {
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 0;
    }
  }
  &-body {
    padding: 35px 30px 40px;
  }
  &-info {
    .title {
      font-size: 1.125rem;
      font-weight: 700;
      margin-top: 28px;
    }
  }
  &-land {
    position: relative;
    padding: 0;
  }
}
.table-property {
  width: 100%;
  font-weight: 500;
  color: #000000;
  text-align: center;
  thead {
    th {
      padding: 12px;
      font-weight: 500;
    }
  }
  tbody {
    td {
      border: 1px solid #e5e5e5;
      &:first-child {
        border-left: none;
      }
      &:last-child {
        border-right: none;
      }
      box-sizing: border-box;
      padding: 20px 14px;
    }
  }
}
.btn-delete {
  cursor: pointer;
  display: flex;
  align-items: center;
  background: #ffffff;
  border: 0.777778px solid #000000;
  border-radius: 5.88235px;
  padding: 10px;
  margin: auto;
  width: 36px;
  height: 36px;
  img {
    width: 100%;
    height: auto;
    min-width: 0.75rem;
  }
}
.contain-table {
  overflow-x: auto;
  @media (max-width: 1024px) {
    overflow-y: hidden;
    overflow-x: auto;
  }
  .table-property {
    width: 100%;
  }
}
.contain-file {
  display: flex;
  align-items: center;
  h3 {
    margin-top: 8px;
    margin-bottom: 0;
  }
}
.btn-upload {
  background: #ffffff;
  white-space: nowrap;
  border: 1px solid #555555;
  box-sizing: border-box;
  border-radius: 5px;
  padding: 5px 19px;
  font-size: 10px;
}
.btn-property {
  padding: 10px;
}
.img-dropdown {
  cursor: pointer;
  width: 18px;
  &__hide {
    transform: rotate(90deg);
    transition: 0.3s;
  }
}
.img-upload {
  margin-left: 20px;
  position: relative;
  width: 123px;
  height: 35px;
  color: #fff;
  background: #faa831;

  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  display: flex;
  justify-content: center;
  align-items: center;
  box-sizing: border-box;
  cursor: pointer;
  input {
    cursor: pointer !important;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    width: 100%;
    opacity: 0;
  }
}
.contain-img {
  height: auto;
  position: relative;
  .img {
    width: 100%;
  }
  .delete {
    position: absolute;
    top: 0;
    right: 0;
    background: #000000;
    color: #ffffff;
    width: 20px;
    height: 20px;
    text-align: center;
    line-height: 1.5;
    cursor: pointer;
    font-weight: 700;
    border-radius: 5px;
  }
}
.contain-total {
  &__left {
    color: #000000;
    .num {
      padding: 0 11px 0 24px;
      width: 340px;
      height: 35px;
      line-height: 1.5;
      border-radius: 5px;
      border: 1px solid #555555;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      background: #f1f1f1 !important;
      cursor: not-allowed;
      user-select: none;
      p {
        margin-bottom: 0;
      }
    }
    .name {
      min-width: 175px;
      margin-bottom: 0;
      margin-right: 20px;
    }
  }
}
.img-locate {
  cursor: pointer;
  position: absolute;
  right: 15px;
  top: 35px;
}
.form-control {
  width: 100%;
}
.btn-orange {
  background: #faa831;
  text-align: center;
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25) !important;
  height: 35px;
  width: 146px;
  color: #fff;
  margin-right: 15px;
  box-sizing: border-box;
  &:hover {
    border-color: #dc8300;
  }
}
.container-title {
  margin: -20px -30px auto;
  padding: 25px 30px 0;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  @media (max-width: 767px) {
    margin: -20px -10px auto;
    padding: 20px 10px 0;
  }
  .title {
    font-size: 1.2rem;
    margin-bottom: 25px;
    font-weight: 700;
    @media (max-width: 767px) {
      font-size: 1.125rem;
    }
  }
  &__footer {
    margin: auto -30px -20px;
    padding: 20px 30px 20px;
    @media (max-width: 767px) {
      margin: auto -10px -20px;
      padding: 20px 10px 0;
      .btn-white {
        margin-bottom: 20px;
      }
    }
  }
}
.contain-img {
  aspect-ratio: 1/1;
  overflow: hidden;
  height: auto;
  position: relative;
  text-align: center;
  margin-bottom: 10px;
  .img {
    object-fit: cover;
    margin-right: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    &-table {
      margin: auto;
      min-width: 50px;
      min-height: 50px;
      width: 50px;
      height: 50px;
      object-fit: cover;
    }
  }
  &__table {
    width: auto;
  }
  .delete {
    position: absolute;
    top: 0;
    right: 0.75rem;
    background: #000000;
    color: #ffffff;
    width: 20px;
    height: 20px;
    text-align: center;
    line-height: 1.5;
    cursor: pointer;
    font-weight: 700;
    border-radius: 5px;
  }
}
.container-img {
  padding: 0.75rem 0;
  border: 1px solid #0b0d10;
}
.loading {
  display: none;
  &__true {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 100vh;
    background: rgba(0, 0, 0, 0.62);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
    &.btn-loading {
      &:after {
        width: 2rem !important;
        height: 2rem !important;
      }
    }
  }
}
.input-disabled {
  min-height: 30px;
  height: 33px;
}
.text-none {
  text-transform: none;
}
.table-property {
  width: 100%;
  font-weight: 500;
  color: #000000;
  text-align: center;
  thead {
    tr {
      border-radius: 0 5px 5px 0;
    }
    th {
      padding: 12px 0;
      font-weight: 700;
      background-color: #f29003;
      color: #ffffff;
      @media (max-width: 787px) {
        padding: 12px;
      }
    }
  }
  tbody {
    td {
      border: 1px solid #e5e5e5;
      &:first-child {
        border-left: none;
      }
      padding: 20px 14px;
    }
  }
  &__order {
    tbody {
      td {
        &:first-child {
          width: 40%;
        }
        &:last-child {
          width: 70px;
        }
        padding: 20px 70px;
        @media (max-width: 1023px) {
          padding: 20px 30px;
        }
      }
    }
  }
}
.container-table {
  border-radius: 5px;
  border: 1px solid #f3f2f7;
}
::-webkit-scrollbar {
  -webkit-appearance: none;
  width: 9px !important;
}
::-webkit-scrollbar-thumb {
  border-radius: 20px;
  background-color: rgba(0, 0, 0, .5);
  box-shadow: 0 0 1px rgba(255, 255, 255, .5);
}
</style>
