import { connect } from "@magento/venia-drivers";
import Search from "./search";
import { executeSearch, toggleSearch } from "../../actions/app";

const mapStateToProps = ({
  app
}) => {
  const {
    searchOpen
  } = app;
  return {
    searchOpen
  };
};

const mapDispatchToProps = {
  executeSearch,
  toggleSearch
};
export default connect(mapStateToProps, mapDispatchToProps)(Search);
//# sourceMappingURL=container.js.map