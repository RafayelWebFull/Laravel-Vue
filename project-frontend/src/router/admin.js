import Index from "../components/Admin/Index";
import BloggerList from "../components/Admin/BloggerList";
import NewCreated from "../components/Admin/NewCreated";



export default [
  {path: '/admin', name: 'Admin',component: Index},
  {path: '/bloggers', name: 'bloggerList',component: BloggerList},
  {path: '/newcreated', name: 'NewCreated',component: NewCreated},

]
