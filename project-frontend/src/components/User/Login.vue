<template>
  <div>
    <div class='container'>
      <h2>Log in</h2>
      <b-alert  v-if="this.loginMassage != null" show variant="danger">{{this.loginMassage }}</b-alert>
      <b-form @submit.prevent="login" @reset="onReset" v-if="show">
        <b-form-group id="input-group-1" label="Email:" label-for="input-1">
          <b-form-input
            id="input-1"
            v-model="$v.form.email.$model"
            :state="validateState('email')"
            type="email"
            required
            placeholder="Enter your Email"
          ></b-form-input>
          <b-form-invalid-feedback
            id="input-1-live-feedback"
          >This is a required field and must be Email</b-form-invalid-feedback>
        </b-form-group>
        <b-form-group id="input-group-2" label="Your Password:" label-for="input-2">
          <b-form-input
            id="input-2"
            v-model="$v.form.password.$model"
            :state="validateState('password')"
            type='password'
            required
            placeholder="Enter your Password"
          ></b-form-input>
          <b-form-invalid-feedback
            id="input-1-live-feedback"
          >This is a required field and must be at least 8 characters.</b-form-invalid-feedback>
        </b-form-group>
        <b-button type="submit" variant="primary" class='btn'>Log in</b-button>
      </b-form>
      <router-link :to="{name: 'Register'}" class="btn btn-success mr-2">
        Don't have an Account?
      </router-link>
    </div>
  </div>
</template>

<script>
import { validationMixin } from "vuelidate";
import { required, minLength ,email } from "vuelidate/lib/validators";
export default {
  mixins: [validationMixin],
  data() {
    return {
      form: {
        email: '',
        password: '',
      },
      show: true,

    }
  },
  mounted() {
    if(this.$store.getters.authUser){
      this.$router.push("/")
    }
    this.$store.commit("storeLoginMassage",null)
  },
  validations: {
    form:{
      email: {
        required,email
      },
      password: {
        required,minLength: minLength(5)
      }
    }
  },
  methods: {
    onReset(evt) {
      evt.preventDefault()
      this.form.email = ''
      this.form.password = ''
      this.show = false
      this.$nextTick(() => {
        this.show = true
      })
    },
    validateState(name) {
      const { $dirty, $error } = this.$v.form[name];
      return $dirty ? !$error : null;
    },
    login(){
      this.loginMassage
      this.$v.form.$touch();
      if (this.$v.form.$anyError) {

      }else{
        return this.$store.dispatch("login",this.form)
      }




    }
  },
  computed: {
    token() {
      return this.$store.getters.loggedIn
    },
    loginMassage(){
      return this.$store.getters.LoginMassage
    }
  }
}
</script>

<style scoped>
.btn {
  float:right;
}
p {
  padding-top: 30px;
}
</style>
