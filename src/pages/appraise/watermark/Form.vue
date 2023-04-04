<template>
  <div>
    <div class="card">
      <div class="card-title">
        <div class="d-flex justify-content-between align-item-center">
          <h3 class="title">Cấu hình tài liệu</h3>
          <img class="img-dropdown" :class="!showDetailDocs ? 'img-dropdown__hide' : ''"
              src="@/assets/images/icon-btn-down.svg" alt="dropdown" @click="() => { showDetailDocs = !showDetailDocs;  }">
        </div>
      </div>
      <div class="card-body card-info" v-show="showDetailDocs">
        <ValidationObserver tag="form" ref="observer" @submit.prevent="validateBeforeSubmit">
          <div class="container-fluid">
          <div class="row container_content pb-2 d-flex ">
            <div class="col-12 color_content  font-weight-bold form-group-container">
							<h4 class="title_config">Cấu hình watermark</h4>
							</div>
              <div class="col-12   pl-4 align-items-center" >
                <div class="d-flex form-group-container">
                  <div class="card-body__info col-6 ">
                    <div class="swith row mb-3">
                      <div class="col-4 information-item">
                      <label class="font-weight-bold  color_content">
                      In watermark
                      </label>
                    <InputSwitchZone v-model="form.is_water_mark" vid="is_water_mark" rules="required" label="In watermark"
                      class="label-none" />
                    </div>
                    <div class="col-4 information-item ">
                      <div>
                        <label class=" font-weight-bold color_content">
                          Độ trong suốt
                      </label>
                        <InputSwitchZone v-model="form.is_transparency" vid="is_transparency" rules="required" label="Độ trong suốt"
                      class="label-none" />
                      </div>
                    </div>
                    </div>
										<div class="combobox row mb-2">
                      <div class="information-item col-12 col-lg-4">
                      <!-- <label class="font-weight-bold  color_content">
                      Tỉ lệ hình ảnh
                      </label> -->
                      <InputCategory v-model="form.scale"  style="width: 70%" label="Tỉ lệ hình ảnh" vid="scale"
                        rules="required" :options="optionScale"  />
                    </div>
                    <div class="information-item col-12 col-lg-4">
                      <InputCategory v-model="form.position_top_bottom"  style="width: 70%" vid="position_top_bottom"
                        rules="required" :options="optionPositionVertical" label="Vị trí theo chiều dọc"/>
                    </div>
                    <div class="information-item col-12 col-lg-4">
                      <InputCategory v-model="form.position_left_right" style="width: 70%"
                        vid="position_left_right" rules="required"
                         :options="optionPositionHorizontal" label="Vị trí theo chiều ngang" />
                    </div>
                    </div>
                    </div>
                    <div class="card-body__avatar col-6">
								<div class="img-avatar mb-3 d-flex justify-content-center">
									<img v-if="form.image !== '' && form.image !== undefined && form.image !== null"
										class="img__avatar" :style="{ opacity: activeChange}" :src="form.image" alt="img" @click="handleViewImage">
								</div>
								<div class="container__input">
									<button type="button" class="btn btn-orange">Thay đổi Image</button>
									<input type="file" id="img" accept="image/png, image/jpeg, image/jpeg, image/jpg" class="input__image"
										@change="onImageChange($event)" />
								</div>
							</div>
                </div>
            </div>
          </div>
          <div class="row container_content pb-2 d-flex align-items-center">
            <div class="col-12  color_content  font-weight-bold form-group-container"><h4 class="title_config">Cấu hình Header</h4></div>
            <div class="row col-12 ">
              <div class="col-12 pl-4 align-items-center">
                <div class="d-flex form-group-container">
                  <div class="col-4 information-item ">
								<label class="font-weight-bold  color_content">
									In Header
										</label>
									<InputSwitchZone v-model="form.is_header" vid="is_header" rules="required" label="In header"
										class="label-none" />
									</div>
							<div class="col-4 information-item ">
										<div>
											<label class="font-weight-bold  color_content">
												Logo công ty
										</label>
											<InputSwitchZone v-model="form.is_logo" vid="is_logo" rules="required" label="Logo công ty"
										class="label-none" />
										</div>
									</div>
									<div class="col-4 information-item ">
										<div>
											<label class="font-weight-bold  color_content">
												Quốc hiệu và tiêu ngữ
										</label>
											<InputSwitchZone v-model="form.is_national_motto" vid="is_national_motto" rules="required" label="Quốc hiệu và tiêu ngữ"
										class="label-none" />
										</div>
									</div>
                </div>
              </div>
            </div>
          </div>

          <div class="row container_content pb-2 d-flex align-items-center">
            <div class="col-12  color_content  font-weight-bold form-group-container"><h4 class="title_config">Cấu hình Footer</h4></div>
            <div class="row col-12">
              <div class="col-12  pl-4 align-items-center ">
                <div class="d-flex form-group-container justify-content-between">
                  <div class="col-4 information-item ">
								<label class="font-weight-bold  color_content">
									In Footer
										</label>
									<InputSwitchZone v-model="form.is_footer" vid="is_water_mark" rules="required" label="In watermark"
										class="label-none" />
									</div>
							<div class="col-4 information-item ">
										<div>
											<label class="font-weight-bold  color_content">
												Đánh số trang
										</label>
											<InputSwitchZone v-model="form.is_page_number" vid="is_page_number" rules="required" label="Đánh số trang"
										class="label-none" />
										</div>
									</div>
							<div class="col-4 information-item ">
										<div>
											<label class="font-weight-bold  color_content">
												Tiêu đề dưới
										</label>
											<InputSwitchZone v-model="form.is_description" vid="is_description" rules="required"
										class="label-none" />
										</div>
									</div>
                </div>
              </div>
            </div>
          </div>
          </div>
          <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
				<div class="d-md-flex d-block button-contain ">
					<button class="btn btn-white btn-orange text-nowrap" :class="{ 'btn-loading disabled': isSubmit }"
						type="submit">
						<img class="img" src="@/assets/icons/ic_save.svg" alt="save">
						Lưu
					</button>
				</div>
			</div>
        </ValidationObserver>
      </div>
    </div>
	<ModalImage v-if="viewImage" :image_detail="this.form.image" @cancel="viewImage = false" />
  </div>
</template>
<script>
import InputText from '@/components/Form/InputText'
import InputPercent from '@/components/Form/InputPercent'
import InputCategory from '@/components/Form/InputCategory'
import Watermark from '@/models/Watermark'
import InputSwitchZone from '@/components/Form/InputSwitchZone'
import ModalImage from '@/components/Modal/ModalImage'
export default {
	name: 'Form',
	components: {
		InputText,
		InputPercent,
		InputCategory,
		InputSwitchZone,
		ModalImage
	},
	data () {
		return {
			viewImage: false,
			isSubmit: false,
			file: null,
			isEdit: false,
			img: null,
			edit: false,
			showDetailDocs: true,
			showCardWaterMark: true,
			showCardHeaderConfig: true,
			showCardFooterConfig: true,
			activeChange: '',
			inputValue1: 1,
			position_top_bottom: [
				{
					id_type: 'top',
					name_type: 'Top'
				},
				{
					id_type: 'center',
					name_type: 'Center'
				},
				{
					id_type: 'bottom',
					name_type: 'Bottom'
				}
			],
			position_left_right: [
				{
					id_type: 'left',
					name_type: 'Left'
				},
				{
					id_type: 'center',
					name_type: 'Center'
				},
				{
					id_type: 'right',
					name_type: 'Right'
				}
			],
			scale: [
				{
					id_type: 100,
					name_type: 'Auto'
				},
				{
					id_type: 500,
					name_type: 500 + ' %'
				},
				{
					id_type: 200,
					name_type: 200 + ' %'
				},
				{
					id_type: 150,
					name_type: 150 + ' %'
				},
				{
					id_type: 75,
					name_type: 50 + ' %'
				}
			],
			form: {
				image: null,
				is_water_mark: false,
				position_top_bottom: '',
				position_left_right: '',
				is_transparency: false,
				scale: ''
			},
			id: ''
		}
	},
	computed: {
		optionPositionHorizontal () {
			return {
				data: this.position_left_right,
				id: 'id_type',
				key: 'name_type'
			}
		},
		optionPositionVertical () {
			return {
				data: this.position_top_bottom,
				id: 'id_type',
				key: 'name_type'
			}
		},
		optionScale () {
			return {
				data: this.scale,
				id: 'id_type',
				key: 'name_type'
			}
		}
	},
	mounted () { },
	methods: {
		formatter (value) {
			return `${value}%`
		},
		onImageChange (e) {
			console.log('eee', e)
			let files = e.target.files || e.dataTransfer.files
			if (!files.length) {
				return
			}
			for (let i = 0; i < e.target.files.length; i++) {
				this.file = e.target.files[i]
				this.createImage()
				this.uploadImage()
			}
			console.log('Upload')
		},
		createImage () {
			let reader = new FileReader()
			let v = this
			reader.onload = (e) => {
				v.image = e.target.result
			}
			reader.readAsDataURL(this.file)
		},
		uploadImage () {
			this.isSubmit = true
			const formData = new FormData()
			formData.append('image', this.file)
			return Watermark.uploadImageWaterMark({ data: formData }).then((response) => {
				if (response && response.data && response.data.data) {
					const timeStamp = new Date().getTime()
					this.form.image = response.data.data.link + '?t=' + timeStamp
					this.isSubmit = false
				} else if (response.data.error) {
					this.isSubmit = false
					this.$toast.open({
						message: response.data.error.message,
						type: 'error',
						position: 'top-right',
						duration: 3000
					})
				}
			})
		},
		handleViewImage () {
			this.viewImage = true
		},
		changeScale (event) {
			this.form.scale = event
		},
		async validateBeforeSubmit () {
			const isValid = await this.$refs.observer.validate()
			if (isValid) {
				this.handleSubmit()
			}
		},
		handleSubmit () {
			this.isSubmit = true
			let data = this.form
			this.updateWatermark(data)
		},
		async updateWatermark (data) {
			try {
				const resp = this.id
					? await Watermark.updateWaterMark(data)
					: await Watermark.create(data)
				if (resp && Object.keys(resp).length) {
					this.$router
						.push({ name: 'watermark.index' })
						.catch((_) => { })
				}
				if (resp.data) {
					this.$toast.open({
						message: 'Cập nhật cấu hình tài liệu thành công',
						type: 'success',
						position: 'top-right'
					})
				} else if (resp.error) {
					this.$toast.open({
						message: resp.error.message,
						type: 'error',
						position: 'top-right'
					})
				}
				this.isSubmit = false
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getWaterMark () {
			const resp = await Watermark.getWaterMark()
			const data = resp.data
			this.form = {
				id: data.id,
				image: data.image,
				is_footer: data.is_footer,
				is_header: data.is_header,
				is_description: data.is_description,
				is_page_number: data.is_page_number,
				is_logo: data.is_logo,
				is_national_motto: data.is_national_motto,
				is_transparency: data.is_transparency,
				position_top_bottom: data.position_top_bottom,
				position_left_right: data.position_left_right,
				is_water_mark: data.is_water_mark,
				scale: data.scale,
				is_name_company: data.is_name_company
			}
		}
	},
	beforeMount () {
	},
	created () {
		const permission = this.$store.getters.currentPermissions
		// fix_permission
		permission.forEach(value => {
			if (value === 'VIEW_ROLE') {
				this.view = true
			}
			if (value === 'ADD_ROLE') {
				this.add = true
			}
			if (value === 'EDIT_ROLE') {
				this.edit = true
			}
			if (value === 'DELETE_ROLE') {
				this.deleted = true
			}
			if (value === 'ACCEPT_ROLE') {
				this.accept = true
			}
		})
		this.id = this.$route.query.id
		if (this.$route.name === 'watermark.index') {
			this.getWaterMark(this.$route.query.id)
		}
	}
}

</script>
<style scoped lang="scss">
  .card {
  border-radius: 5px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
  background: #FFFFFF;
  margin-bottom: 5rem;

  &-footer {
    padding: 15px 24px;
  }

  &-title {
    padding: 15px;
    margin-bottom: 0;
    color: #E8E8E8;
    border-bottom: 2px solid;
    &__img {
      padding: 8px 20px;
    }
    h3 {
      color: #007EC6;
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
    &__avatar {
			display: flex;
			flex-direction: column;
			align-items: center;
		}
  }

  &-info {
    .title {
      font-size: 1.125rem;
      font-weight: 700;
      margin-top: 28px;

    }
  }
}
.img-avatar {
	img {
		object-fit: cover;
		width: 25vw;
	}
}
.title_config{
	font-size: 1.1rem;
	color: #007EC6;
  font-weight: 600;
  margin-bottom: 0;
}
.container {
	&__input {
		position: relative;

		.input {
			&__image {
				position: absolute;
				width: 100%;
				top: 0;
				bottom: 0;
				left: 0;
				right: 0;
				opacity: 0;
				cursor: pointer;
			}
		}
	}
}
.form-group-container {
  margin-top: 10px;
}
.container_content {
    border-bottom: 1px solid #E8E8E8;
  }

.img-dropdown {
  cursor: pointer;
  width: 18px;

  &__hide {
    transform: rotate(90deg);
    transition: .3s;
  }
}
</style>
