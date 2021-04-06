import Index from "../components/Blogger/Index";
import newPosts from "../components/Blogger/newPosts";
import Edit from "../components/Blogger/Edit";
import Show from "../components/Blogger/Show";

export default [
  {path: '/blogger', name: 'BloggerIndex',component: Index},
  {path: '/newPosts', name: 'NewPosts',component: newPosts},
  {path: '/editPost', name: 'EditPost',component: Edit},
  {path: '/showPost', name: 'ShowPost',component: Show},
]
