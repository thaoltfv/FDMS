<template>
    <div class="card px-4 py-2">
        <ValidationObserver
            tag="form"
            ref="observer"
            @submit.prevent="validateBeforeSubmit"
        >
            <div class="card-body">
                <div class="card-body__avatar">
                    <div class="img-avatar mb-3">
                        <img v-if="form.link === '' || form.link === undefined || form.link === null" class="w-100 " src="../../../assets/icons/ic_company.png" alt="avatar">
                        <img v-if="form.link !== '' && form.link !== undefined && form.link !== null" class="w-100  img__avatar" :src="form.link" alt="img" @click="handleViewImage">
                    </div>
                    <div class="container__input" v-if="!id || (id && isEdit)">
                        <button type="button" class="btn btn-orange">Thay đổi Logo</button>
                        <input
                            type="file"
                            id="img"
                            accept="image/png, image/jpeg, image/jpeg, image/jpg"
                            class="input__image"
                        />
                    </div>

                </div>
                <div class="card-body__info">
                    <!-- <div class="divContainer"> -->
                      <div style="margin-bottom: 30px" class="row">
                        <div class="col-12 col-lg-4">
                          <p class="name">Tên công ty</p>
                          {{form.name}}
                        </div>

                      <!-- <div class="divContainer"> -->
                        <div class="col-12 col-lg-4">
                          <p class="name">Tên viết tắt</p>
                          {{form.acronym}}
                        </div>

                        <div class="col-12 col-lg-4">
                          <p class="name">Xuống dòng sau</p>
                          {{form.down_line}}
                        </div>

                      </div>

                    <div style="margin-bottom: 30px" class="divContainer">
                         <p class="name">Địa chỉ</p>
                        {{form.address}}
                    </div>
                    <!-- <InputText
                        v-model="form.name"
                        label="Tên công ty"
                        vid="name"
                        rules="required"
                        class="information-item"
                        :disabledInput=" id && !isEdit"
                        :class="{'text-label-disabled':id && !isEdit}"
                    />
                    <InputText
                        v-model="form.acronym"
                        label="Tên viết tắt"
                        vid="acronym"
                        rules="required"
                        class="information-item"
                        :disabledInput=" id && !isEdit"
                        :class="{'text-label-disabled':id && !isEdit}"
                    />
                    <InputText
                        v-model="form.address"
                        label="Địa chỉ"
                        vid="address"
                        rules="required"
                        class="information-item"
                        :disabledInput=" id && !isEdit"
                        :class="{'text-label-disabled':id && !isEdit}"
                    /> -->
                    <div style="margin-bottom: 30px" class="row">
                        <div class="col-12 col-lg-4">
                            <p class="name">Số điện thoại</p>
                            {{form.phone_number}}
                            <!-- <InputText
                                v-model="form.phone_number"
                                label="Số điện thoại"
                                vid="phone_number"
                                type="number"
                                :max-length="11"
                                rules="required"
                                class="information-item"
                                :disabledInput=" id && !isEdit"
                                :class="{'text-label-disabled':id && !isEdit}"
                            /> -->
                        </div>
                        <div class="col-12 col-lg-4">
                            <p class="name">Fax</p>
                            {{form.fax_number}}
                            <!-- <InputText
                                v-model="form.fax_number"
                                label="Fax"
                                vid="fax_number"
                                type="number"
                                :max-length="20"
                                rules="required"
                                class="information-item"
                                :disabledInput=" id && !isEdit"
                                :class="{'text-label-disabled':id && !isEdit}"
                            /> -->
                        </div>
                        <div class="col-12 col-lg-4">
                            <p class="name">Giám đốc công ty</p>
                            {{appraiserName}}
                            <!-- <InputCategory
                                v-model="form.appraiser_id"
                                :options="optionAppraisers"
                                label="Giám đốc công ty"
                                rules="required"
                                class="information-item"
                                :disabled=" id && !isEdit"
                                :class="{'text-label-disabled':id && !isEdit}"
                            /> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
                <div class="d-md-flex d-block button-contain ">
                    <button class="btn btn-white" type="button" @click="handleCancel">
                        <img class="img" src="../../../assets/icons/ic_cancel.svg" alt="cancel">
                        Trở về
                    </button>
                    <button v-if="edit" class="btn btn-white" @click.prevent="handleEdit(form.id)">
                      <img class="img" src="../../../assets/icons/ic_edit.svg" alt="edit">
                      Chỉnh sửa
                    </button>
                    <!-- <button
                    :disabled=" id && !isEdit"
                    class="btn btn-white btn-orange text-nowrap"
                    :class="{'btn-loading disabled': isSubmit}"
                    type="submit">
                        <img class="img" src="../../../assets/icons/ic_save.svg" alt="save">
                        Lưu
                    </button> -->
                </div>
            </div>
        </ValidationObserver>
        <ModalImage
            v-if="viewImage"
            :image_detail="form.link"
            @cancel="viewImage = false"
        />
    </div>
</template>

<script>
import UploadDragDrop from '@/components/file/UploadDragDrop'
import File from '@/models/File'
import InputText from '@/components/Form/InputText'
import InputCategory from '@/components/Form/InputCategory'
import AppraiserCompany from '@/models/AppraiserCompany'
import ModalImage from '@/components/Modal/ModalImage'

export default {
	name: 'Detail',
	components: {
		UploadDragDrop,
		InputText,
		InputCategory,
		ModalImage
	},
	data () {
		return {
			viewImage: false,
			isSubmit: false,
			file: null,
			branch_name: '',
			form: {
				link: null,
				name: '',
				acronym: '',
				address: '',
				phone_number: '',
				fax_number: '',
				appraiser_id: '',
				down_line: 0
			},
			id: '',
			appraisers: [],
			isEdit: false,
			appraiserName: '',
			view: false,
			add: false,
			edit: false,
			deleted: false,
			accept: false
		}
	},
	computed: {
		optionAppraisers () {
			return {
				data: this.appraisers,
				id: 'id',
				key: 'name'
			}
		}
	},
	mounted () {},
	methods: {
		handleCancel () {
			this.$router.push({ name: 'appraiser-company.index' })
		},
		handleViewImage () {
			this.viewImage = true
		},
		onImageChange (e) {
			let files = e.target.files || e.dataTransfer.files
			if (!files.length) {
				return
			}
			for (let i = 0; i < e.target.files.length; i++) {
				this.file = e.target.files[i]
				this.createImage()
				this.uploadImage()
			}
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
			return File.upload({ data: formData }).then((response) => {
				if (response && response.data && response.data.data) {
					const item = {
						link: response.data.data.link,
						picture_type: response.data.data.picture_type
					}
					this.form.link = item.link
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
		async validateBeforeSubmit () {
			const isValid = await this.$refs.observer.validate()
			if (isValid) {
				this.handleSubmit()
			}
		},
		handleSubmit () {
			this.isSubmit = true
			let data = this.form
			this.createCompany(data)
		},
		async createCompany (data) {
			try {
				const resp = this.id
					? await AppraiserCompany.update(data)
					: await AppraiserCompany.create(data)

				if (resp && Object.keys(resp).length) {
					this.$router
						.push({ name: 'appraiser-company.index' })
						.catch((_) => {})
				}
				if (resp.data) {
					this.$toast.open({
						message: this.id
							? 'Cập nhật thông tin công ty thành công'
							: 'Thêm mới thông tin công ty thành công',
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
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getAppraisers () {
			try {
				const reps = await AppraiserCompany.getAppraisers()
				this.appraisers = [...reps.data]
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		},
		async getCompanies (id) {
			this.isLoading = true
			try {
				const resp = await AppraiserCompany.detail(id)
				const data = resp.data
				this.form = {
					id: id,
					link: data.link,
					name: data.name,
					acronym: data.acronym,
					address: data.address,
					phone_number: data.phone_number,
					fax_number: data.fax_number,
					appraiser_id: data.appraiser_id,
					down_line: data.down_line
				}
				this.appraiserName = data.appraiser.name
				this.isLoading = false
			} catch (e) {
				this.isLoading = false
			}
		},
		async handleEdit (id) {
			this.$router.push({ name: 'appraiser-company.edit', query: { id: id, type: 'edit' } }).catch(_ => {})
		}
	},
	beforeMount () {
		this.getAppraisers()
	},
	watch: {
		id (val) {
			if (val) {
				this.getCompanies(val)
			}
		}
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
		// this.isEdit = this.$route.query.type === 'edit'
	}
}
</script>

<style lang="scss" scoped>
.text-none {
    text-transform: none;
}
.card {
    padding-bottom: 47px !important;
    box-shadow: none;
    border: none;
    &-title {
        font-weight: 700;
        padding-bottom: 47px;
    }
    &-body {
        align-items: center;
        @media (max-width: 766px) {
            grid-column-gap: 20px;
        }
        &__avatar {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        &__info {
            .information-item {
                padding: 5px 0;
                &__title {
                }
            }
        }
    }
}
.name {
    font-weight: 700;
    font-size: 1.125rem;
    margin-bottom: 0px !important;
}
.img-avatar {
    width: 120px;
    height: 120px;
    img {
        object-fit: cover;
    }
}
.divContainer {
  margin-bottom: 20px;
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
.btn {
    &__change-password {
        margin-right: 10px;
    }
}
.information {
    display: flex;
    flex-direction: row;
    margin: auto;
    @media (max-width: 766px) {
        display: block;
    }
    &-item {
        font-size: 1.125rem;
        margin: 0 45px;
        @media (max-width: 766px) {
            padding: 7.5px 0;
        }
        &__title {
            min-width: 147px;
            font-size: 1.125rem;
            font-weight: 600;
            color: #555555;
            display: block;
        }
    }
}
.action {
    margin-bottom: 47px;
}
.img__avatar {
    cursor: pointer;
}
.name {
    &__user {
        font-size: 1.125rem;
        font-weight: 700;
        color: #000000;
    }
}
</style>
