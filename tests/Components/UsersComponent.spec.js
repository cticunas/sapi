import { mount } from '@vue/test-utils'
import UserComponent from '../../resources/js/components/UsersComponent.vue'

// const mount = require('@vue/test-utils');
// const UserComponent = require('../../resources/js/components/UsersComponent.vue')

describe('UsersComponent.vue', () => {
    test('clicking button Ingresar', () => {
      const wrapper = mount(UserComponent)
    //   expect(wrapper.text()).toContain('counter: 0')
    //   wrapper.find('button').trigger('click')
    })
  })
  