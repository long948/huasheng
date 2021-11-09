import { asyncRoutes, constantRoutes } from '@/router'

function filterChildren(children, roles, parentPath) {
  const res = []
  children.forEach(route => {
    let path = parentPath + '/' + route.path
    if (route.path.indexOf('/:') != -1) {
      const realPath = route.path.substring(0, route.path.indexOf('/:'))
      path = parentPath + '/' + realPath
    }
    if (roles.includes(path)) {
      res.push(route)
    }
  })
  return res
}

/**
 * Filter asynchronous routing tables by recursion
 * @param routes asyncRoutes
 * @param roles
 */
export function filterAsyncRoutes(routes, roles) {
  if (roles == '*') return routes
  const res = []
  routes.forEach(route => {
    const tmp = { ...route }
    if (roles.includes(tmp.path)) {
      if (tmp.children) {
        tmp.children = filterChildren(tmp.children, roles, tmp.path)
      }
      if (tmp.children.length > 0) {
        res.push(tmp)
      }
    }
  })
  return res
}

const state = {
  routes: [],
  addRoutes: []
}

const mutations = {
  SET_ROUTES: (state, routes) => {
    state.addRoutes = routes
    state.routes = constantRoutes.concat(routes)
  }
}

const actions = {
  generateRoutes({ commit }, roles) {
    return new Promise(resolve => {
      let accessedRoutes
      if (roles.includes('admin')) {
        accessedRoutes = asyncRoutes || []
      } else {
        accessedRoutes = filterAsyncRoutes(asyncRoutes, roles)
      }
      commit('SET_ROUTES', accessedRoutes)
      resolve(accessedRoutes)
    })
  }
}

export default {
  namespaced: true,
  state,
  mutations,
  actions
}
