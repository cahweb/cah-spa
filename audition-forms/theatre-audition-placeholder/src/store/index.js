/* eslint no-undef: 1 */

import Vue from 'vue'
import Vuex from 'vuex'

import axios from 'axios'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    programReqs: {},
    programReqsUri: wpVars.programReqsUri,
    specialty: wpVars.spec,
  },

  getters: {
  },

  mutations: {
    updateSpec(state, spec) {
      Vue.set(state, 'specialty', spec)
    },

    updateProgramReqs(state, {spec, reqs}) {
      const programReqs = state.programReqs

      programReqs[spec] = reqs

      Vue.set(state, 'programReqs', programReqs)
    },
  },

  actions: {
    async init({dispatch}) {
      dispatch('getSpec')
        .then(() => {dispatch('getProgramReqs')})
    },

    async getSpec({commit}) {
      const spec = document.querySelector('#spec').value

      commit('updateSpec', spec)
    },

    async getProgramReqs({commit, state}) {
      const uri = `${state.programReqsUri}/${state.specialty}-program-reqs.json`

      const reqs = await axios.get(uri)
        .then(response => response.data)

      const payload = {spec: state.specialty, reqs}

      commit('updateProgramReqs', payload)
    },
  },

  modules: {}
})
