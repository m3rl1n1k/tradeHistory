/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
// any CSS you import will output into a single css file (app.css in this case)
import {startStimulusApp} from '@symfony/stimulus-bridge';
import './styles/app.css';


import './js/user_blockInputs'
import './js/user_settings'
import './js/search'

export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.([jt])sx?$/
));