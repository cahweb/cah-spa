<template>
  <div id="app" class="container">
    <div class="buttons d-flex flex-row justify-content-between flex-wrap w-100 mb-3">
      <div v-for="(degree, i) of degreeList" :key="i">
        <button type="button" class="btn btn-primary btn-sm mb-2" :class="{active: Object.keys(selected).length > 0 && selected.abbr == degree.abbr}" @click.prevent="changeReqs(degree.abbr)">{{ degree.name }}</button>
      </div>
    </div>
    <div v-if="!!selected && Object.keys(selected).length > 0" id="reqInfo">
      <h2 class="heading-underline">Program Requirements for {{ selected.name }}</h2>
      <div v-if="specialty === 'theatre'">
        <a class="btn btn-primary mb-3" href="https://performingarts.cah.ucf.edu/audition-theatre" target="_blank" rel="noopener">Apply Now</a>
        <div id="preamble">
          <p v-if="programCoordinator"><strong>Program Coordinator: {{ programCoordinator.name }}</strong> <a :href="`mailto:${programCoordinator.email}`">{{ programCoordinator.email }}</a></p>
          <p v-if="selected.level === 'undergrad'" id="undergrad-blurb"> Students must apply and be accepted through both the UCF School of Performing Arts and <a href="https://www.ucf.edu/admissions/undergraduate">UCF Undergraduate Admissions</a>. It is encouraged to apply to both UCF Undergraduate Admissions and the UCF School of Performing Arts as early as possible. The school can provisionally accept students prior to being accepted by UCF Undergraduate Admissions but cannot offer official acceptance of study until students receive their UCF acceptance.</p>
        </div>
        <div v-html="programCopy" class="mb-3" />
        <div id="postamble">
          <p><em>We don't want application fees to be a barrier. If you are submitting an application through AcceptD and need assistance, contact the coordinator for the program you are applying to for more information. For information about application fee waivers for UCF admissions, <a href="https://www.ucf.edu/admissions/undergraduate/question/how-can-i-request-a-waiver-of-the-application-fee/" target="_blank" rel="noopener">click here</a>.</em></p>
        </div>
      </div>
      <div v-else-if="specialty === 'music'">
        <a class="btn btn-primary mb-3" href="https://performingarts.cah.ucf.edu/audition-music" target="_blank" rel="noopener">Apply Now</a>
        <div v-html="programCopy" class="mb-3" id="programReqs" />
      </div>
    </div>
  </div>
</template>

<script>
import {mapActions, mapState} from 'vuex'

export default {
  name: 'App',

  data() {
    return {
      degreeLookup: {
        music: {
          undergrad: {
            'ba-music': 'Bachelor of Arts, Music',
            'bm-performance': 'Bachelor of Music (Performance Track)',
            'bm-jazz': 'Bachelor of Music (Jazz Track)',
            'bm-composition': 'Bachelor of Music (Composition Track)',
            'bme': 'Bachelor of Music Education',
          },
          grad: {
            'ma-music': 'Master of Arts, Music Studies',
            'ma-conducting': 'Master of Arts, Conducting',
          },
        },
        theatre: {
          undergrad: {
            'ba-theatre': 'B.A. Theatre',
            'bfa-acting': 'BFA Acting',
            'bfa-design-tech': 'BFA Design & Technology',
            'bfa-musical-theatre': 'BFA Musical Theatre',
            'bfa-stage-mgmt': 'BFA Stage Management',
          },
          grad: {
            'ma-theatre': 'M.A. Theatre',
            'ma-music-theatre': 'M.A. Musical Theatre',
            'mfa-acting': 'MFA Acting',
            'mfa-young-theatre': 'MFA Theatre for Young Audiences',
            'mfa-themed-exp': 'MFA Themed Experience',
          }
        },
      },
      programCoordinators: {
        undergrad: {
          'ba-theatre': {
            name: 'Kristina Tollefson',
            email: 'kristina.tollefson@ucf.edu',
          },
          'bfa-acting': {
            name: 'Be Boyd',
            email: 'belinda.boyd@ucf.edu',
          },
          'bfa-design-tech': {
            name: 'Bert Scott',
            email: 'bert.scott@ucf.edu',
          },
          'bfa-musical-theatre': {
            name: 'Earl D. Weaver',
            email: 'earl.weaver@ucf.edu',
          },
          'bfa-stage-mgmt': {
            name: 'Claudia Lynch',
            email: 'claudia.lynch@ucf.edu',
          },
        },
        grad: {
          'ma-theatre': {
            name: 'Julia Listengarten',
            email: 'julia.listengarten@ucf.edu',
          },
          'ma-music-theatre': {
            name: 'Earl D. Weaver',
            email: 'earl.weaver@ucf.edu',
          },
          'mfa-acting': {
            name: 'Michael Wainstein',
            email: 'michael.wainstein@ucf.edu',
          },
          'mfa-young-theatre': {
            name: 'Vandy Wood',
            email: 'vandy.wood@ucf.edu',
          },
          'mfa-themed-exp': {
            name: 'Peter Weishar',
            email: 'peter.weishar@ucf.edu',
          },
        },
      },
      selected: {},
    }
  },

  computed: {
    degreeList() {
      const reqs = Object.entries(this.degreeLookup[this.specialty])
      const degrees = []

      if (reqs !== undefined && reqs.length > 0) {
        for (const [level, list] of reqs) {
          for (const [abbr, name] of Object.entries(list)) {
            const newDegree = {abbr, name, level}
            degrees.push(newDegree)
          }
        }
      }

      return degrees
    },
    programCopy() {
      const programs = this.programReqs[this.specialty][this.selected.level]

      let html = '';

      let lines = []

      if (programs !== undefined) {
        if (this.specialty === 'music') {
          const base = programs.text
          const specReqs = programs[this.selected.abbr]

          lines = specReqs !== undefined ? [...base, ...specReqs] : base
        }
        else if (this.specialty === 'theatre') {
          lines = programs[this.selected.abbr]
        }

        html = lines.join("\n")
      }

      return html
    },
    programCoordinator() {
      const coordinator = this.programCoordinators[this.selected.level][this.selected.abbr]

      if (coordinator !== undefined) {
        return coordinator
      }

      return false
    },
    ...mapState([
      'specialty',
      'programReqs',
    ])
  },

  methods: {
    changeReqs(target) {
      for (const degree of this.degreeList) {
        if (target === degree.abbr) {
          this.selected = degree
          break
        }
      }
    },
    ...mapActions([
      'init',
    ])
  },

  created() {
    this.init()
  },
}
</script>

<style lang="scss">
#programReqs {

    /deep/ ul {
        list-style-type: disc;

        li {
            margin-bottom: .5em;
        }
    }

    /deep/ ol {
        list-style-type: none;
        counter-reset: item;

        >li {
            border-left: 2px solid #fc0;
            padding-left: 1em;
            margin-bottom: 2em;

            &::before {
                font-family: "Archer A", "Archer B", "UCF Slab Serif Alt", serif;
                font-size: 4rem;
                float: left;
                margin-left: -.95em;
                margin-top: -.4em;
                counter-increment: item;
                content: counter(item);
            }
        }
    }
}
</style>
