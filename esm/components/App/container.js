import { connect } from "@magento/venia-drivers";
import appActions, { closeDrawer } from "../../actions/app";
import App from "./app";

const mapStateToProps = ({
  app,
  unhandledErrors
}) => ({
  app,
  unhandledErrors
});

const {
  markErrorHandled
} = appActions;
const mapDispatchToProps = {
  closeDrawer,
  markErrorHandled
};
export default connect(mapStateToProps, mapDispatchToProps)(App);
//# sourceMappingURL=container.js.map