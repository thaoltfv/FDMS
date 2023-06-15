<template>
  <div class="login" v-if="layout === 'auth'">
<!--      <ValidationObserver v-slot="{ handleSubmit }">-->
        <section id="firebaseui-auth-container" />
<!--      </ValidationObserver>-->
    </div>
</template>

<script>
import firebase from 'firebase/app'
import 'firebase/auth'
import firebaseui from 'firebaseui'
import 'firebaseui/dist/firebaseui.css'

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
			error: null
		}
	},
	async created () {

	},
	mounted () {
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
	},
	computed: {
		layout () {
			return this.$store.state.layout
		}
	},

	methods: {
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
    height: 100vh;
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
    border-radius: 5px;
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
		height: 100vh;
	}
</style>
