import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import { authBasic, authAdmin, authAdvisor, authAuthor, authAuthorOnly } from './hooks/useAuth'

import Login from './views/auth/Login.vue'
import ForgotPassword from './views/auth/ForgotPassword.vue'
import Dashboard from './views/Dashboard.vue'
import Courses from './views/admin/Courses.vue'
import Course from './views/admin/Course.vue'
import Schooling from './views/admin/Schooling.vue'
import Schoolings from './views/admin/Schoolings.vue'
import Users from './views/admin/Users.vue'
import User from './views/author/User.vue'
import NotFound from './views/NotFound.vue'
import Submissions from './views/advisor/Submissions.vue'
import MyCourses from './views/advisor/MyCourses.vue'
import Submit from './views/author/Submit.vue'
import Settings from './views/admin/Settings.vue'
import Events from './views/author/Events.vue'
import Corrections from './views/author/Corrections.vue'
import Historic from './views/author/Historic.vue'
import Register from './views/auth/Register.vue'
import MyCertificates from './views/author/MyCertificates.vue'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'Home',
    component: Login,
    meta: { layout: 'empty' },
  },
  /*

    ====== Auth routes ======

  */
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { layout: 'empty' },
  },
  {
    path: '/forgotpassword',
    name: 'ForgotPassword',
    component: ForgotPassword,
    meta: { layout: 'empty' },
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { layout: 'empty' }
  },
  /*
  
      ====== Admin routes ======
  
  */
  {
    path: '/courses',
    name: 'Courses',
    component: Courses,
    beforeEnter: authAdmin,
  },
  {
    path: '/course/:action',
    name: 'Course',
    component: Course,
    beforeEnter: authAdmin,
  },
  {
    path: '/schoolings',
    name: 'Schoolings',
    component: Schoolings,
    beforeEnter: authAdmin,
  },
  {
    path: '/schooling/:action',
    name: 'Schooling',
    component: Schooling,
    beforeEnter: authAdmin,
  },
  {
    path: '/users',
    name: 'Users',
    component: Users,
    beforeEnter: authAdmin
  },
  {
    path: '/settings',
    name: 'Configurações de evento',
    component: Settings,
    beforeEnter: authAdmin
  },
  /*
  
      ====== Advisor routes ======
  
  */
  {
    path: '/submissions',
    name: 'Submissions',
    component: Submissions,
    beforeEnter: authBasic
  },
  {
    path: '/mycourses',
    name: 'MyCourses',
    component: MyCourses,
    beforeEnter: authAdvisor
  },
  {
    path: '/submit/:articleid?',
    name: 'Submit',
    component: Submit,
    beforeEnter: authBasic
  },
  /*
  
      ====== Author routes ======
  
  */
  {
    path: '/events',
    name: 'Events',
    component: Events,
    beforeEnter: authBasic
  },
  {
    path: '/corrections',
    name: 'Corrections',
    component: Corrections,
    beforeEnter: authAuthorOnly
  },
  {
    path: '/historic/:userid?',
    name: 'Historic',
    component: Historic,
    beforeEnter: authAuthor
  },
  {
    path: '/mycertificates',
    name: 'MyCertificates',
    component: MyCertificates,
    beforeEnter: authBasic
  },

  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    beforeEnter: authBasic,
  },
  {
    path: '/user/:action',
    name: 'User',
    component: User,
    beforeEnter: authBasic,
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFound,
    meta: { layout: 'empty' },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes: routes,
});

export default router;
