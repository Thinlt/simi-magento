import React from 'react'
import { Query } from 'src/drivers';
import {addRequestVars} from 'src/simi/Helper/Network'
import { useApolloContext } from '@magento/peregrine'
import { useQueryResult } from '@magento/peregrine'
import { useCallback, useMemo } from 'react';
import _regeneratorRuntime from "@babel/runtime/regenerator";
import _asyncToGenerator from "@babel/runtime/helpers/asyncToGenerator";
import _slicedToArray from "@babel/runtime/helpers/slicedToArray";
import _objectSpread from "@babel/runtime/helpers/objectSpread";

export const Simiquery = props => {
    let modProps = {}
    const variables = props.variables?props.variables:{}
    modProps.variables = addRequestVars(variables)
    modProps = {...modProps, ...props}
    return <Query {...modProps} >
        {props.children}
    </Query>
}

export var simiUseQuery = function simiUseQuery(query) {
    var apolloClient = useApolloContext();

    var _useQueryResult = useQueryResult(),
        _useQueryResult2 = _slicedToArray(_useQueryResult, 2),
        queryResultState = _useQueryResult2[0],
        queryResultApi = _useQueryResult2[1];

    var receiveResponse = queryResultApi.receiveResponse; // define a callback that performs a query
    // either as an effect or in response to user interaction

    var runQuery = useCallback(
    /*#__PURE__*/
    function () {
        var _ref2 = _asyncToGenerator(
        /*#__PURE__*/
        _regeneratorRuntime.mark(function _callee(_ref) {
        var variables, payload;
        return _regeneratorRuntime.wrap(function _callee$(_context) {
            while (1) {
            switch (_context.prev = _context.next) {
                case 0:
                variables = _ref.variables;
                //simi
                variables = addRequestVars(variables)
                //end
                _context.next = 3;
                return apolloClient.query({
                    query: query,
                    variables: variables
                });

                case 3:
                payload = _context.sent;
                receiveResponse(payload);

                case 5:
                case "end":
                return _context.stop();
            }
            }
        }, _callee);
        }));

        return function (_x) {
        return _ref2.apply(this, arguments);
        };
    }(), [query, receiveResponse]); // this object should never change

    var api = useMemo(function () {
        return _objectSpread({}, queryResultApi, {
        runQuery: runQuery
        });
    }, [queryResultApi, runQuery]);
    return [queryResultState, api];
}
