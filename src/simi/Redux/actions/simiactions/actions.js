import { createActions } from 'redux-actions';

const prefix = 'SIMIACTIONS';
const actionTypes = [
    'SAMPLE_ACTION',
];

export default createActions(...actionTypes, { prefix });
