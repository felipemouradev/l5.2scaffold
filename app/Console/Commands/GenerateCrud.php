<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class GenerateCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geracrud {table} {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $table = $this->argument('table');
        $table2 = "'".$table."';";
        $module = camel_case($this->argument('module'));
        $model = ucfirst(camel_case($table));
        $fields = Schema::getColumnListing($table);
        $primaryKey = $fields[0];
        $baseModules = dirname(dirname(dirname(__DIR__)))."/Modules/".ucfirst($module);
        //salva o model
        $templateModel = file_get_contents(storage_path('/template/template_model'));
        $templateModel = $this->generateModel($templateModel,$table2,$model,$fields,$module);
        file_put_contents($baseModules."/Entities/".$model.".php",$templateModel);
        //
        //salva repositorio
        $templateRepository = file_get_contents(storage_path('/template/template_repositories'));
        $templateRepository = $this->generateRepository($templateRepository,$model,$module,$primaryKey);
        file_put_contents($baseModules."/Repositories/".$model."Repository.php",$templateRepository);
        //
        //salva controller
        $templateController = file_get_contents(storage_path('/template/template_controller'));
        $templateController = $this->generateController($templateController,$module,$model);
        file_put_contents($baseModules."/Http/Controllers/".$model."Controller.php",$templateController);
        //
        //atualiza route
        $route = file_get_contents($baseModules."/Http/routes.php");
        $route = $this->generateRoute($route,$table,$model);
        file_put_contents($baseModules."/Http/routes.php",$route);
    }

    public function generateRoute($route,$table,$model)
    {
        $rotas = file_get_contents(storage_path('/template/routes_routes'));
        $rotas = str_replace('[TABLE]',$table,$rotas);
        $rotas = str_replace('[MODEL]',$model,$rotas);
        $route = str_replace('//[CODE]',$rotas,$route);
        return $route;
    }

    public function generateController($template,$module,$model)
    {
        $template_function = file_get_contents(storage_path('/template/function_controller_crud'));
        $template_function = str_replace('[MODEL]',$model,$template_function);
        $template = str_replace('[MODEL]',$model,$template);
        $template = str_replace('[MODULE]',ucfirst($module),$template);
        $template = str_replace('[CODE]',$template_function,$template);

        return $template;
    }

    public function generateRepository($template,$model,$module,$primaryKey)
    {
        $template_function = file_get_contents(storage_path('/template/function_repositories_entities'));
        $template_function = str_replace('[MODULE]',$module,$template_function);
        $template_function = str_replace('[MODEL]',$model,$template_function);
        $template_function = str_replace('[PRIMARY_KEY]',$primaryKey,$template_function);
        $template = str_replace('[MODULE]',ucfirst($module),$template);
        $template = str_replace('[MODEL]',$model,$template);
        $template = str_replace('[ENTITY]',$model,$template);
        $template = str_replace('[CODE]',$template_function,$template);

        return $template;
    }

    public function generateModel($template,$table,$model,$fields,$module)
    {
        $getFillable = function($template,$table,$model,$fields,$module){
            $fillable = "[";
            $i=0;
            $final = count($fields);

            $otherkeys = [];
            foreach($fields as $column) {
                if(strrpos($column,"_id")==true and $i>0){
                    $otherkeys[] = $column;
                }
                if(($final-1)==$i) {
                    $fillable .= "'".$column."'";
                } else {
                    $fillable .= "'".$column."',";
                }
                $i++;
            }
            $fillable .="];";
            $templateFunction = file_get_contents(storage_path('/template/function_belongsto'));

            $getFunctionsForeing = function($otherkeys,$templateFunction,$module,$model) {
                $templateF = "";
                foreach ($otherkeys as $foreing){
                    $part = array_reverse(explode("_",$foreing));
                    unset($part[0]);
                    $foreingTable = "";
                    foreach ($part as $name){
                        $foreingTable .= ucfirst($name);
                    }
                    $f = $templateFunction;
                    $f = str_replace('[MODULE]',ucfirst($module),$f);
                    $f = str_replace('[MODEL]',$model,$f);
                    $f = str_replace('[FOREING_MODEL]',$foreingTable,$f);
                    $templateF .= "\n\n".$f;
                }
                return $templateF;
            };
            $templateF = $getFunctionsForeing($otherkeys,$templateFunction,$module,$table,$model);

            return ['fillable'=>$fillable,'foreings_functions'=>$templateF];
        };
        $data = $getFillable($template,$table,$model,$fields,$module);
        $template = str_replace('[PRIMARY_KEY]',$fields[0],$template);
        $template = str_replace("[MODEL]",$model,$template);
        $template = str_replace("[MODULE]",ucfirst($module),$template);
        $template = str_replace("[FIELDS]",$data['fillable'],$template);
        $template = str_replace("[TABLE]",$table,$template);
        $template = str_replace("[CODE]",$data['foreings_functions'],$template);
        $template = str_replace("[VALIDADE]",file_get_contents(storage_path('/template/function_model_validation')),$template);
        return $template;
    }

}
