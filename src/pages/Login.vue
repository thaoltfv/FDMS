<template>
  <div v-if="layout === 'auth'" :class="isMobile() ? 'background_news_mobile' : 'login'">
<!--      <ValidationObserver v-slot="{ handleSubmit }">-->
        <section v-if="!isMobile()" id="firebaseui-auth-container" />
        <div v-else class="login-mobile" >
          <section style="display:none;" id="firebaseui-auth-container" />
          <div class="row" style="margin-top: 40%">
            <div class="col-12">
              <div class="card card-login">
                <div class="container-title" style="justify-content: center;display: flex;">
                  <div class="d-flex">
                    <h2 class="title">ĐĂNG NHẬP</h2>
                  </div>
                </div>
                <div class="contain-detail">
                  <div class="divider"></div>
                  <div
                    class="text-right font-italic my-1"
                    style="
                      color: #617f9e;
                      font-weight: 300;
                      font-size: 13px;
                      letter-spacing: 0em;
                    "
                  >
                    Thông tin có dấu <b class="text-red">(*)</b> là bắt buộc
                  </div>
                  <InputText
                    v-model="new_email"
                    vid="new_email"
                    label="Email"
                    class="col-12 form-group-container"
                    rules="required"
                    :required=true
                  />
                  <InputText
                    v-model="new_password"
                    vid="new_password"
                    label="Mật khẩu"
                    class="col-12 form-group-container"
                    rules="required"
                    type="password"
                    :required=true
                  />
                </div>
                <div class="d-flex" style="justify-content: center;margin-top: 20px;margin-bottom: 20px;">
                  <div class="row">
                    <button :class="{ 'btn_loading disabled': isSubmit }" class="btn btn-white btn-orange text-nowrap" @click.prevent="clickLogin">
                        Đăng nhập
                      </button>
                  </div>
                </div>
                
              </div>
            </div>
          </div>
        </div>
<!--      </ValidationObserver>-->
    </div>
</template>

<script>
import firebase from 'firebase/app'
import 'firebase/auth'
import firebaseui from 'firebaseui'
import 'firebaseui/dist/firebaseui.css'
import InputText from '@/components/Form/InputText'
import $ from 'jquery'

export default {
	name: 'Login',
	data () {
		return {
			hidePassword: true,
			isSubmit: false,
			submitted: false,
			form: {
				login_id: '',
				login_password: '',
				company_code: ''
			},
			error: null,
      new_email: '',
      new_password: '',
		}
	},
  components: {
    InputText
  },
	async created () {

	},
	mounted () {
    // if (!this.isMobile()){
      const uiConfig = {
			signInSuccessUrl: '/verify',
			signInOptions: [
				{
					provider: firebase.auth.EmailAuthProvider.PROVIDER_ID,
					requireDisplayName: false,
					signInMethod: firebase.auth.EmailAuthProvider.EMAIL_LINK_SIGN_IN_METHOD,
					forceSameDevice: false
				}
			],
			credentialHelper: firebaseui.auth.CredentialHelper.NONE,
      // callbacks: {
      //   uiShown: function() {
      //     document.querySelector('.firebaseui-button').style.setProperty("background-color", "#00507c", "important");
      //     document.querySelector('.firebaseui-button').style.setProperty("color", "#FFFFFF", "important");
      //     document.querySelector('.firebaseui-button').innerHTML = 'TIẾP TỤC';
      //   }
      // }
		}
		const ui = new firebaseui.auth.AuthUI(firebase.auth())
		ui.start('#firebaseui-auth-container', uiConfig)
    // }
	},
	computed: {
		layout () {
			return this.$store.state.layout
		}
	},

	methods: {
    isMobile() {
			if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
				return true
			} else {
				return false
			}
		},
    clickLogin(){
      console.log('data', this.new_email,this.new_password)
      $('[name="email"]').val(this.new_email)
      $('[class="firebaseui-id-submit firebaseui-button mdl-button mdl-js-button mdl-button--raised mdl-button--colored"]').click()
      
      setTimeout(() => {
        console.log('lỗi email', $('[class="firebaseui-info-bar-message"]').text())
        if ($('[class="firebaseui-info-bar-message"]').text() !== ''){
          this.$toast.open({
            message: 'Email không tồn tại, vui lòng thử lại',
            type: 'error',
            position: 'top-right'
          })
          $('[class="firebaseui-info-bar-message"]').text('')
        } else {
          $('[name="password"]').val(this.new_password)
          // console.log('gọi login',$('[name="email"]') ,$('[name="password"]'))
          $('[class="firebaseui-id-submit firebaseui-button mdl-button mdl-js-button mdl-button--raised mdl-button--colored"]').click()
          // console.log('lỗi nè',$('[class="firebaseui-error firebaseui-text-input-error firebaseui-id-password-error firebaseui-hidden"]').text())
          setTimeout(() => {
            console.log('lỗi mật khẩu', $('[class="firebaseui-error firebaseui-text-input-error firebaseui-id-password-error"]').text())
            if ($('[class="firebaseui-error firebaseui-text-input-error firebaseui-id-password-error"]').text() == 'You have entered an incorrect password too many times. Please try again in a few minutes.'){
              this.$toast.open({
                message: 'Bạn đã nhập sai quá nhiều lần, vui lòng thử lại sau ít phút',
                type: 'error',
                position: 'top-right'
              })
              $('[class="firebaseui-error firebaseui-text-input-error firebaseui-id-password-error"]').text('')
            } else if ($('[class="firebaseui-error firebaseui-text-input-error firebaseui-id-password-error"]').text() == "The email and password you entered don't match") {
              this.$toast.open({
                message: 'Mật khẩu không đúng, vui lòng thử lại',
                type: 'error',
                position: 'top-right'
              })
              $('[class="firebaseui-error firebaseui-text-input-error firebaseui-id-password-error"]').text('')
            }
          },1000)
        }
        // $('[class="firebaseui-id-submit firebaseui-button mdl-button mdl-js-button mdl-button--raised mdl-button--colored"]').click()
        
        // console.log('lỗi nè',$('[class="firebaseui-info-bar firebaseui-id-info-bar"]').length)
      },1000)
      
      // let email = $('[name="email"]').value
      // console.log('email', email)
    }
	}
}
</script>

<style lang="scss" scoped>
	.form-label {
		font-weight: 700;
	}
  .login {
    background: url("../assets/images/im_background.jpg") no-repeat top center #2d494d;
    // background: url("../assets/images/im_background.png") no-repeat ;
    // background-size: contain;
    background-size: cover;
    height: 100dvh;
    display: flex;
    justify-content: center;
    align-items: center;
    @media (max-width: 768px) {
      padding: 20px;
    }
    span {
      //&:nth-child(1) {
      //  display: flex;
      //  align-items: center;
      //  height: 100%;
      //  width: 100%;
      //  justify-content: center;
      //}
    }

    &-error {
      width: 100%;
      margin-top: .25rem;
      font-size: 87.5%;
      color: #cd201f;
    }

    .form-footer {
      margin-top: 0;
      .btn {
        margin: 40px 0 0px;
        &-loading{
          color: #354052 !important;
        }
      }
    }
  }
  .img-logo{
    width: 50%;
    margin-bottom: 72px;
  }
  .card-body{
    border-radius: 10px;
    margin: auto;
    width: 407px;
    max-width: 100%;
    background: #FFFFFF;
    box-shadow: 0px 1px 4px rgba(0, 0, 0, 0.25);
    padding: 25px;
  }
  .form-control{
    position: relative;
    vertical-align: top;

    height: 42px;
    border: 1px solid #DDD;
    color: #626262;
  }
  .error{

  }
	#firebaseui-auth-container {
		display: flex;
		height: 100dvh;
	}
  .background_news_mobile {
    height: 100dvh;
  width: 100%;
  background-repeat: no-repeat;
  background-size: 100%;
  background-image: linear-gradient(
      to bottom,
      rgba(255, 255, 255, 0),
      rgb(255, 255, 255)
    ),
    url(https://firebasestorage.googleapis.com/v0/b/fast-value.appspot.com/o/assets%2Fbackground-signup_signin-mobile.png?alt=media&token=129523df-feeb-4d86-b61f-d8582f3c296d);
}
.card-login {
  background: rgba(255, 255, 255, 0.1);
  box-shadow: 9px 4px 22px rgba(10, 54, 78, 0.25) !important;
  backdrop-filter: blur(10px);
  border-radius: 20px;
  margin-bottom: -20px;
}
.login-mobile {
	position: fixed;
	z-index: 200;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	.card {
		border-radius: 5px;
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
		max-width: 900px;
		width: 100%;
		max-height: 90vh;
		margin-bottom: 0;
		// padding: 35px 50px;
		padding: 20px 30px 20px;
		// @media (max-width: 787px) {
		// 	padding: 20px 10px;
		// }
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
.container-title{
	.title{
		color:#007EC6;
		// margin-top:20px;
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
.divider {
  border-style: solid;
  border-width: thin 0 0;
  display: block;
  flex: 1 1 100%;
  height: 0;
  max-height: 0;
  transition: inherit;
}
.form-group-container {
    margin-top: 15px;
  }
</style>
