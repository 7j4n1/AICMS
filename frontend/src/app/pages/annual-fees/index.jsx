// Import Dependencies
import { useEffect, useState, useCallback } from "react";
import { Link } from "react-router";
import { PlusIcon, MagnifyingGlassIcon, PencilIcon, TrashIcon } from "@heroicons/react/24/outline";
import { toast } from "sonner";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Input } from "components/ui";
import { annualFeesAPI } from "services/api";

// ----------------------------------------------------------------------

export default function AnnualFeesList() {
 const [annualFees, setAnnualFees] = useState([]);
 const [loading, setLoading] = useState(true);
 const [search, setSearch] = useState("");
 const [yearFilter, setYearFilter] = useState("");

 const fetchAnnualFees = useCallback(async () => {
   try {
     setLoading(true);
     const params = {
       per_page: 25,
     };
     
     if (search) {
       params.coop_id = search;
     }
     
     if (yearFilter) {
       params.year = yearFilter;
     }

     const response = await annualFeesAPI.getAll(params);
     const data = response.data?.data || [];
     setAnnualFees(data);
   } catch (error) {
     console.error("Error fetching annual fees:", error);
     toast.error("Failed to load annual fees");
   } finally {
     setLoading(false);
   }
 }, [search, yearFilter]);

 useEffect(() => {
   fetchAnnualFees();
 }, [fetchAnnualFees]);

 const handleDelete = async (id) => {
   if (!window.confirm("Are you sure you want to delete this annual fee record?")) {
     return;
   }

   try {
     await annualFeesAPI.delete(id);
     toast.success("Annual fee deleted successfully");
     fetchAnnualFees();
   } catch (error) {
     console.error("Error deleting annual fee:", error);
     toast.error("Failed to delete annual fee");
   }
 };

 const formatCurrency = (amount) => {
   return new Intl.NumberFormat("en-NG", {
     style: "currency",
     currency: "NGN",
   }).format(amount);
 };

 return (
   <Page title="Annual Fees">
     <div className="space-y-5">
       {/* Header */}
       <div className="flex items-center justify-between">
         <div>
           <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
             Annual Fees Management
           </h2>
           <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
             Manage annual member fees
           </p>
         </div>
         <Link to="/annual-fees/create">
           <Button color="primary" className="gap-2">
             <PlusIcon className="size-5" />
             Add Annual Fee
           </Button>
         </Link>
       </div>

       {/* Filters */}
       <Card className="p-5">
         <div className="flex gap-3">
           <div className="flex-1">
             <Input
               placeholder="Search by Coop ID..."
               value={search}
               onChange={(e) => setSearch(e.target.value)}
               prefix={
                 <MagnifyingGlassIcon className="size-5 text-gray-400" />
               }
             />
           </div>
           <Input
             type="number"
             placeholder="Filter by year..."
             value={yearFilter}
             onChange={(e) => setYearFilter(e.target.value)}
             className="w-48"
           />
         </div>
       </Card>

       {/* Annual Fees Table */}
       <Card className="overflow-hidden">
         <div className="overflow-x-auto">
           <table className="w-full">
             <thead className="bg-gray-50 dark:bg-dark-600">
               <tr>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Coop ID
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Year
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Annual Savings
                 </th>
                 <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Annual Fee
                 </th>
                 <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                   Actions
                 </th>
               </tr>
             </thead>
             <tbody className="divide-y divide-gray-200 bg-white dark:divide-dark-500 dark:bg-dark-700">
               {loading ? (
                 <tr>
                   <td colSpan="5" className="px-6 py-4 text-center">
                     Loading...
                   </td>
                 </tr>
               ) : annualFees.length === 0 ? (
                 <tr>
                   <td colSpan="5" className="px-6 py-4 text-center text-gray-500">
                     No annual fees found
                   </td>
                 </tr>
               ) : (
                 annualFees.map((fee) => (
                   <tr key={fee.id} className="hover:bg-gray-50 dark:hover:bg-dark-600">
                     <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-dark-100">
                       {fee.coopId}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {fee.annual_year}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {formatCurrency(fee.annual_savings)}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                       {formatCurrency(fee.annual_fee)}
                     </td>
                     <td className="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                       <div className="flex items-center justify-end gap-2">
                         <Link to={`/annual-fees/${fee.id}/edit`}>
                           <Button size="sm" variant="outlined" className="gap-1">
                             <PencilIcon className="size-4" />
                             Edit
                           </Button>
                         </Link>
                         <Button
                           size="sm"
                           variant="outlined"
                           color="error"
                           className="gap-1"
                           onClick={() => handleDelete(fee.id)}
                         >
                           <TrashIcon className="size-4" />
                           Delete
                         </Button>
                       </div>
                     </td>
                   </tr>
                 ))
               )}
             </tbody>
           </table>
         </div>
       </Card>
     </div>
   </Page>
 );
}