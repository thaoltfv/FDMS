<template>
	<ValidationProvider tag="div" :name="label" :vid="vid" v-slot="{ errors }">
		<label class="form-label font-weight-bold"> {{ $t("Loại sơ bộ") }}</label>

		<div :class="{ has__error: errors[0] }">
			<a-select
				show-search
				style="width: 100%"
				:value="value === '' ? undefined : value"
				:placeholder="$t('select_option_empty')"
				@change="handleChange"
			>
				<a-select-option value="">
					{{ $t("select_option_empty") }}
				</a-select-option>
				<a-select-option value="Cơ bản">
					{{ $t("Cơ bản") }}
				</a-select-option>
				<a-select-option value="Nâng cao">
					{{ $t("Nâng cao") }}
				</a-select-option>
			</a-select>

			<!--Message Error-->
			<span v-if="errors[0]" class="errors">{{ errors[0] }}</span>
		</div>
	</ValidationProvider>
</template>

<script>
export default {
	name: "InputCategoryGender",

	model: {
		prop: "value",
		event: "change"
	},

	props: {
		label: {
			type: String,
			required: true,
			default: ""
		},

		vid: {
			type: String,
			default: ""
		},

		value: {
			type: [String, Number],
			required: true,
			default: ""
		}
	},

	methods: {
		handleChange(value) {
			console.log("value", value);
			this.$emit("change", value);
		}
	}
};
</script>

<style scoped></style>
