import Register from "../components/User/Register";
import Profile from "../components/User/Profile";
import Login from "../components/User/Login";
import Index from "../components/Blogger/Index";

export default [
  {path: '/register', name: 'Register',component: Register},
  {path: '/profile', name: 'Profile',component: Profile},
  {path: '/login', name: 'Login',component: Login},
]
