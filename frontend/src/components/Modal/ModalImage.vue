<template>
  <div
    class="modal-delete d-flex justify-content-center align-items-center"
    @click.self="handleCancel">
    <div class="card">
      <img :src="image_detail" alt="">
    </div>
  </div>
</template>

<script>
export default {
	name: 'ModalImage',
	props: ['image_detail'],
	data () {
		return {
			image: ''
		}
	},
	methods: {
		handleCancel (event) {
			this.$emit('cancel', event)
		},

		handleAction (event) {
			this.$emit('action', event)
			this.$emit('cancel', event)
		}
	}
}
</script>

<style lang="scss" scoped>
.modal-delete {
  position: fixed;
  z-index: 10002;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,.6);
  .card {
    margin-bottom: 0;
    background: transparent;
    img{
      object-fit: contain;
      max-width: 800px;
      max-height: 90vh;
    }
    &-body {
      text-align: center;
      padding: 40px;
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
</style>
