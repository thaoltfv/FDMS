<template>
    <div class="pannel card">
      <ValidationObserver tag="form"
                          ref="observer"
                          @submit.prevent="validateBeforeSubmit">
      <InputText
        v-model="form.description"
        placeholder="Nhập tên chi tiết"
        rules="required|max:200"
        :max-length="200"
        class="mb-3"
        vid="description"
        label="Tên chi tiết"
      />
      <div class="btn-footer d-md-flex d-block justify-content-end align-items-center">
        <div class="d-md-flex d-block button-contain ">
          <button class="btn btn-white" @click="onCancel" type="button">
            <img class="img" src="../../../assets/icons/ic_cancel.svg" alt="cancel">
            Trở về
          </button>
          <button class="btn btn-white btn-orange text-nowrap" :class="{'btn-loading disabled': isSubmit}" type="submit">
            <img class="img" src="../../../assets/icons/ic_save.svg" alt="save">
            Lưu
          </button>
        </div>
      </div>
      </ValidationObserver>
    </div>
</template>
<script>
import InputText from '@/components/Form/InputText'
import InputCategory from '@/components/Form/InputCategory'
import Dictionary from '@/models/Dictionary'

export default {
	props: {
		detail: {
			type: Object,
			default: () => {
			}
		}
	},
	name: 'Form',
	components: {
		InputText,
		InputCategory
	},
	computed: {
	},
	data () {
		return {
			form: {
				description: '',
				type: ''
			},
			isSubmit: false,
			province: null,
			district: null,
			ward: null,
			street: null
		}
	},
	created () {
		this.form.type = this.$route.query.type
	},
	methods: {
		async validateBeforeSubmit () {
			const isValid = await this.$refs.observer.validate()
			if (isValid) {
				this.handleSubmit()
			}
		},
		handleSubmit () {
			this.isSubmit = true
			let data = this.form
			this.createDictionary(data)
		},

		onCancel () {
			window.history.go(-1)
		},

		async createDictionary (data) {
			try {
				const resp = await Dictionary.create(data)

				if (resp && Object.keys(resp).length) {
					window.history.go(-1)
				}
				this.$toast.open({
					message: 'Thêm mới thành công',
					type: 'success',
					position: 'top-right'
				})
			} catch (err) {
				this.isSubmit = false
				throw err
			}
		}
	},
	beforeMount () {
	}
}
</script>
<style scoped lang="scss">
.pannel{
  background: #FFFFFF;
  box-shadow: 1px 2px 0 #e5eaee;
  border-radius: 5px;
  padding: 25px;
  margin-bottom: 40px;
  &__table{
    padding: 25px 0;
    border-radius: 5px;
  }
  &__input{
    p{
      color: #5a5386;
      font-weight: 600;
    }
  }
}
.form-control{
  width: 100%;
  margin-right: 5px;
  color: #555555;
  border-radius: 5px;

  @media (max-width: 1023px) {
    width: 100%;
  }
  &:focus{
    border-color: #CCCCCC;
    box-shadow: none;
  }
}
.coordinate-img{
  cursor: pointer;
  position: absolute;
  right: 10px;
  top: 30px;
  background: #FFFFFF;
  height: 2.295rem;
  width: 32px;
  display: grid;
  place-items: center;
  img {
    height: 60%;
  }
}
</style>
