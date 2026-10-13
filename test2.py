from git import Repo
repo_url= "https://github.com/deepinsight/insightface.git"
local_dir="./my_cloned_repo"
cloned_repo=Repo.clone_from(repo_url,local_dir)
printf(f"Repository successfully cloned to: {cloned_repo.working_tree_dir}")